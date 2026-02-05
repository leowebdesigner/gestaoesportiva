<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShowTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_show_team(): void
    {
        $team = Team::factory()->create();
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['teams:read']);

        $response = $this->getJson("/api/v1/teams/{$team->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $team->id);
    }
}
