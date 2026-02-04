<?php

namespace Tests\Feature\Player;

use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdatePlayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_player(): void
    {
        $user = User::factory()->create();
        $player = Player::factory()->create();

        Sanctum::actingAs($user, ['players:update']);

        $response = $this->putJson("/api/v1/players/{$player->id}", [
            'first_name' => 'Updated',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('players', ['id' => $player->id, 'first_name' => 'Updated']);
    }
}
