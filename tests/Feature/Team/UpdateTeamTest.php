<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_update_team(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        Sanctum::actingAs($user, ['teams:update']);

        $response = $this->putJson("/api/v1/teams/{$team->id}", [
            'city' => 'New City',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('teams', ['id' => $team->id, 'city' => 'New City']);
    }
}
