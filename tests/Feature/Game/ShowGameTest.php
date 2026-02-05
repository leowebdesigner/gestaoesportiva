<?php

namespace Tests\Feature\Game;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShowGameTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_show_game(): void
    {
        $game = Game::factory()->create();
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['games:read']);

        $response = $this->getJson("/api/v1/games/{$game->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $game->id);
    }
}
