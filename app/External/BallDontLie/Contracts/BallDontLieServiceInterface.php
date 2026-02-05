<?php

namespace App\External\BallDontLie\Contracts;

interface BallDontLieServiceInterface
{
    /**
     * @return array{data: array, meta: array}
     */
    public function fetchTeams(int $page = 1, int $perPage = 100): array;

    /**
     * @param array<string, mixed> $params
     * @return array{data: array, meta: array}
     */
    public function fetchPlayers(array $params = []): array;

    /**
     * @param array<string, mixed> $params
     * @return array{data: array, meta: array}
     */
    public function fetchGames(array $params = []): array;
}
