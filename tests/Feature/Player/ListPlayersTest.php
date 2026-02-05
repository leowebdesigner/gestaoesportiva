<?php

namespace Tests\Feature\Player;

use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListPlayersTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_players(): void
    {
        Player::factory()->count(5)->create();
        $user = User::factory()->user()->create();

        Sanctum::actingAs($user, ['players:read']);

        $response = $this->getJson('/api/v1/players');

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }
}
