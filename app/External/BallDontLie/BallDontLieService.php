<?php

namespace App\External\BallDontLie;

use App\External\BallDontLie\Contracts\BallDontLieClientInterface;
use App\External\BallDontLie\DTOs\GameDTO;
use App\External\BallDontLie\DTOs\PlayerDTO;
use App\External\BallDontLie\DTOs\TeamDTO;

class BallDontLieService
{
    public function __construct(
        private BallDontLieClientInterface $client
    ) {}

    public function fetchTeams(int $page = 1, int $perPage = 100): array
    {
        $response = $this->client->getTeams($page, $perPage);

        return [
            'data' => array_map(fn ($item) => TeamDTO::fromArray($item), $response['data'] ?? []),
            'meta' => $response['meta'] ?? [],
        ];
    }

    /**
     * @param array $params
     */
    public function fetchPlayers(array $params = []): array
    {
        $response = $this->client->getPlayers($params);

        return [
            'data' => array_map(fn ($item) => PlayerDTO::fromArray($item), $response['data'] ?? []),
            'meta' => $response['meta'] ?? [],
        ];
    }

    public function fetchGames(array $params = []): array
    {
        $response = $this->client->getGames($params);

        return [
            'data' => array_map(fn ($item) => GameDTO::fromArray($item), $response['data'] ?? []),
            'meta' => $response['meta'] ?? [],
        ];
    }
}
