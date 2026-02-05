<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_team(): void
    {
        $admin = User::factory()->admin()->create();
        $team = Team::factory()->create();

        Sanctum::actingAs($admin, ['teams:*']);

        $response = $this->deleteJson("/api/v1/teams/{$team->id}");

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.deleted', true);
        $this->assertSoftDeleted('teams', ['id' => $team->id]);
    }
}
