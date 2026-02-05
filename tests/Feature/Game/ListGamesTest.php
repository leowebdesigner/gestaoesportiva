<?php

namespace Tests\Feature\Game;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListGamesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_games(): void
    {
        Game::factory()->count(5)->create();
        $user = User::factory()->user()->create();

        Sanctum::actingAs($user, ['games:read']);

        $response = $this->getJson('/api/v1/games');

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }
}
