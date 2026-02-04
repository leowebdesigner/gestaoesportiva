<?php

namespace Tests\Feature\Team;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreateTeamTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_team(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['teams:create']);

        $response = $this->postJson('/api/v1/teams', [
            'name' => 'Lakers',
            'city' => 'Los Angeles',
            'abbreviation' => 'LAL',
            'conference' => 'West',
            'division' => 'Pacific',
            'full_name' => 'Los Angeles Lakers',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('teams', ['name' => 'Lakers']);
    }
}
