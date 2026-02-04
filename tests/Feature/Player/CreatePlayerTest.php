<?php

namespace Tests\Feature\Player;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreatePlayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_player(): void
    {
        $user = User::factory()->create();
        $team = Team::factory()->create();

        Sanctum::actingAs($user, ['players:create']);

        $response = $this->postJson('/api/v1/players', [
            'first_name' => 'LeBron',
            'last_name' => 'James',
            'team_id' => $team->id,
            'position' => 'F',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('players', ['first_name' => 'LeBron', 'last_name' => 'James']);
    }
}
