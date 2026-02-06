<?php

namespace App\External\BallDontLie;

use App\Exceptions\ExternalApiException;
use App\Exceptions\RateLimitExceededException;
use App\External\BallDontLie\Contracts\BallDontLieClientInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;


final class BallDontLieClient implements BallDontLieClientInterface
{
    private const THROTTLE_KEY = 'balldontlie-api';

    public function getTeams(int $page = 1, int $perPage = 100): array
    {
        return $this->get('/teams', [
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }

    public function getPlayers(array $params = []): array
    {
        return $this->get('/players', $params);
    }

    public function getGames(array $params = []): array
    {
        return $this->get('/games', $params);
    }

    private function get(string $uri, array $query = []): array
    {
        $limit = (int) config('balldontlie.rate_limit.requests', 30);
        $window = (int) config('balldontlie.rate_limit.window', 60);

        return Redis::throttle(self::THROTTLE_KEY)
            ->allow($limit)
            ->every($window)
            ->then(
                fn (): array => $this->executeRequest($uri, $query),
                fn (): never => throw new RateLimitExceededException(
                    retryAfterSeconds: $window,
                    message: 'BallDontLie API rate limit exceeded'
                )
            );
    }

    private function executeRequest(string $uri, array $query): array
    {
        /** @var Response $response */
        $response = Http::timeout((int) config('balldontlie.timeout', 30))
            ->withHeaders([
                'Authorization' => (string) config('balldontlie.api_key'),
            ])
            ->get($this->baseUrl() . $uri, $query);

        if ($response->status() === 429) {
            $retryAfter = (int) ($response->header('Retry-After') ?: config('balldontlie.rate_limit.window', 60));

            throw new RateLimitExceededException(
                retryAfterSeconds: $retryAfter,
                message: 'BallDontLie API returned 429 Too Many Requests'
            );
        }

        if (!$response->successful()) {
            throw new ExternalApiException(
                'Error in BallDontLie: ' . $response->status(),
                $response->status()
            );
        }

        return $response->json();
    }

    private function baseUrl(): string
    {
        return rtrim((string) config('balldontlie.base_url'), '/');
    }
}
