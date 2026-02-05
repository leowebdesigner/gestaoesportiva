<?php

namespace Tests\Feature\Team;

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TeamPlayersTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_team_players(): void
    {
        $team = Team::factory()->create();
        Player::factory()->count(3)->create(['team_id' => $team->id]);
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['players:read']);

        $response = $this->getJson("/api/v1/teams/{$team->id}/players");

        $response->assertOk();
        $response->assertJsonPath('success', true);
    }
}
