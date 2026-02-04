<?php

namespace Tests\Feature\Game;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateGameTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_game(): void
    {
        $user = User::factory()->create();
        $home = Team::factory()->create();
        $visitor = Team::factory()->create();

        Sanctum::actingAs($user, ['games:create']);

        $response = $this->postJson('/api/v1/games', [
            'home_team_id' => $home->id,
            'visitor_team_id' => $visitor->id,
            'season' => 2023,
            'status' => 'Scheduled',
            'game_date' => now()->toDateString(),
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('games', ['season' => 2023]);
    }
}
