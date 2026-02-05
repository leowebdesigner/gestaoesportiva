<?php

namespace Tests\Feature\Game;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateGameTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_game(): void
    {
        $user = User::factory()->create();
        $game = Game::factory()->create();

        Sanctum::actingAs($user, ['games:update']);

        $response = $this->putJson("/api/v1/games/{$game->id}", [
            'status' => 'Final',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('games', ['id' => $game->id, 'status' => 'Final']);
    }
}
