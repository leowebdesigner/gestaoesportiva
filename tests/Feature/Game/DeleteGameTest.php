<?php

namespace Tests\Feature\Game;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteGameTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_game(): void
    {
        $admin = User::factory()->admin()->create();
        $game = Game::factory()->create();

        Sanctum::actingAs($admin, ['games:*']);

        $response = $this->deleteJson("/api/v1/games/{$game->id}");

        $response->assertOk();
        $response->assertJsonPath('message', 'Game deleted successfully.');
        $response->assertJsonPath('data.id', $game->id);
        $response->assertJsonPath('data.deleted', true);
        $this->assertSoftDeleted('games', ['id' => $game->id]);
    }
}
