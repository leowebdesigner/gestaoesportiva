<?php

namespace Tests\Unit\External;

use App\External\BallDontLie\BallDontLieClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BallDontLieClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_teams_returns_array(): void
    {
        Http::fake([
            '*' => Http::response(['data' => []], 200),
        ]);

        $client = new BallDontLieClient();
        $result = $client->getTeams();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }
}
