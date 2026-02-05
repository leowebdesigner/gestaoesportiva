<?php

namespace App\External\BallDontLie;

use App\Exceptions\ExternalApiException;
use App\External\BallDontLie\Contracts\BallDontLieClientInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class BallDontLieClient implements BallDontLieClientInterface
{
    private int $requestCount = 0;
    private float $windowStart;

    public function __construct()
    {
        $this->windowStart = microtime(true);
    }

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
        $this->respectRateLimit();

        /** @var Response $response */
        $response = Http::retry(
            config('balldontlie.retry.times', 3),
            config('balldontlie.retry.sleep', 1000)
        )
            ->timeout(config('balldontlie.timeout', 30))
            ->withHeaders([
                'Authorization' => config('balldontlie.api_key'),
            ])
            ->get($this->baseUrl() . $uri, $query);

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

    private function respectRateLimit(): void
    {
        $limit = (int) config('balldontlie.rate_limit.requests', 30);
        $window = (int) config('balldontlie.rate_limit.window', 60);

        $elapsed = microtime(true) - $this->windowStart;

        if ($elapsed >= $window) {
            $this->requestCount = 0;
            $this->windowStart = microtime(true);
        }

        if ($this->requestCount >= $limit) {
            $sleepTime = $window - $elapsed;
            if ($sleepTime > 0) {
                sleep((int) ceil($sleepTime));
            }
            $this->requestCount = 0;
            $this->windowStart = microtime(true);
        }

        $this->requestCount++;
    }
}
