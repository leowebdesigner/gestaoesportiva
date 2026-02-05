<?php

namespace Tests\Feature\Player;

use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PlayerAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_player(): void
    {
        $admin = User::factory()->admin()->create();
        $player = Player::factory()->create();

        Sanctum::actingAs($admin, ['*']);

        $response = $this->deleteJson("/api/v1/players/{$player->id}");

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.deleted', true);
        $this->assertSoftDeleted('players', ['id' => $player->id]);
    }

    public function test_user_cannot_delete_player(): void
    {
        $user = User::factory()->user()->create();
        $player = Player::factory()->create();

        Sanctum::actingAs($user, ['players:read', 'players:create', 'players:update', 'players:delete']);

        $response = $this->deleteJson("/api/v1/players/{$player->id}");

        $response->assertForbidden();
        $response->assertJson([
            'success' => false,
            'message' => 'Only administrators can delete players.',
        ]);
    }

    public function test_user_can_create_player(): void
    {
        $user = User::factory()->user()->create();

        Sanctum::actingAs($user, ['players:create']);

        $response = $this->postJson('/api/v1/players', [
            'first_name' => 'LeBron',
            'last_name' => 'James',
        ]);

        $response->assertCreated();
    }

    public function test_user_can_update_player(): void
    {
        $user = User::factory()->user()->create();
        $player = Player::factory()->create();

        Sanctum::actingAs($user, ['players:update']);

        $response = $this->putJson("/api/v1/players/{$player->id}", [
            'first_name' => 'Updated Name',
        ]);

        $response->assertOk();
    }
}
