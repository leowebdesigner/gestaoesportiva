<?php

namespace Tests\Feature\Player;

use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShowPlayerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_show_player(): void
    {
        $player = Player::factory()->create();
        $user = User::factory()->create();

        Sanctum::actingAs($user, ['players:read']);

        $response = $this->getJson("/api/v1/players/{$player->id}");

        $response->assertOk();
        $response->assertJsonPath('data.id', $player->id);
    }
}
