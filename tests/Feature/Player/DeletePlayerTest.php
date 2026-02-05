<?php

namespace Tests\Feature\Player;

use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeletePlayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_player(): void
    {
        $admin = User::factory()->admin()->create();
        $player = Player::factory()->create();

        Sanctum::actingAs($admin, ['players:*']);

        $response = $this->deleteJson("/api/v1/players/{$player->id}");

        $response->assertOk();
        $response->assertJsonPath('message', 'Player deleted successfully.');
        $response->assertJsonPath('data.id', $player->id);
        $response->assertJsonPath('data.deleted', true);
        $this->assertSoftDeleted('players', ['id' => $player->id]);
    }
}
