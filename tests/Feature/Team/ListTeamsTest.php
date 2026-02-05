<?php

namespace Tests\Feature\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListTeamsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_teams(): void
    {
        Team::factory()->count(5)->create();
        $user = User::factory()->user()->create();

        Sanctum::actingAs($user, ['teams:read']);

        $response = $this->getJson('/api/v1/teams');

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }
}
