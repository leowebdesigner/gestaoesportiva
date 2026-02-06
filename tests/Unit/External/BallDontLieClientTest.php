<?php

namespace Tests\Unit\External;

use App\Exceptions\RateLimitExceededException;
use App\External\BallDontLie\BallDontLieClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Mockery;
use Tests\TestCase;

class BallDontLieClientTest extends TestCase
{
    public function test_get_teams_returns_array(): void
    {
        $this->mockRedisThrottleAllow();

        Http::fake([
            '*' => Http::response(['data' => []], 200),
        ]);

        $client = new BallDontLieClient();
        $result = $client->getTeams();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    public function test_get_players_returns_array(): void
    {
        $this->mockRedisThrottleAllow();

        Http::fake([
            '*' => Http::response(['data' => []], 200),
        ]);

        $client = new BallDontLieClient();
        $result = $client->getPlayers(['per_page' => 25]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    public function test_get_games_returns_array(): void
    {
        $this->mockRedisThrottleAllow();

        Http::fake([
            '*' => Http::response(['data' => []], 200),
        ]);

        $client = new BallDontLieClient();
        $result = $client->getGames(['season' => 2023]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
    }

    public function test_throws_rate_limit_exception_when_throttle_limit_exceeded(): void
    {
        $this->mockRedisThrottleDeny();

        $this->expectException(RateLimitExceededException::class);
        $this->expectExceptionMessage('BallDontLie API rate limit exceeded');

        $client = new BallDontLieClient();
        $client->getTeams();
    }

    public function test_throws_rate_limit_exception_when_api_returns_429(): void
    {
        $this->mockRedisThrottleAllow();

        Http::fake([
            '*' => Http::response([], 429, ['Retry-After' => '30']),
        ]);

        $this->expectException(RateLimitExceededException::class);
        $this->expectExceptionMessage('BallDontLie API returned 429 Too Many Requests');

        $client = new BallDontLieClient();
        $client->getTeams();
    }

    public function test_rate_limit_exception_contains_retry_after_seconds(): void
    {
        $this->mockRedisThrottleAllow();

        Http::fake([
            '*' => Http::response([], 429, ['Retry-After' => '45']),
        ]);

        try {
            $client = new BallDontLieClient();
            $client->getTeams();
            $this->fail('Expected RateLimitExceededException was not thrown');
        } catch (RateLimitExceededException $e) {
            $this->assertEquals(45, $e->retryAfterSeconds);
        }
    }

    /**
     * Mock Redis::throttle to always allow requests.
     */
    private function mockRedisThrottleAllow(): void
    {
        $throttleMock = Mockery::mock();
        $throttleMock->shouldReceive('allow')->andReturnSelf();
        $throttleMock->shouldReceive('every')->andReturnSelf();
        $throttleMock->shouldReceive('then')
            ->andReturnUsing(fn (callable $success, callable $failure = null) => $success());

        Redis::shouldReceive('throttle')
            ->andReturn($throttleMock);
    }

    /**
     * Mock Redis::throttle to deny requests (rate limit exceeded).
     */
    private function mockRedisThrottleDeny(): void
    {
        $throttleMock = Mockery::mock();
        $throttleMock->shouldReceive('allow')->andReturnSelf();
        $throttleMock->shouldReceive('every')->andReturnSelf();
        $throttleMock->shouldReceive('then')
            ->andReturnUsing(fn (callable $success, callable $failure) => $failure());

        Redis::shouldReceive('throttle')
            ->andReturn($throttleMock);
    }
}
