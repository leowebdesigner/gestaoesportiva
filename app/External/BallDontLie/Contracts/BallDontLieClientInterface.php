<?php

namespace App\External\BallDontLie\Contracts;

interface BallDontLieClientInterface
{
    public function getTeams(int $page = 1, int $perPage = 100): array;

    public function getPlayers(array $params = []): array;

    public function getGames(array $params = []): array;
}
