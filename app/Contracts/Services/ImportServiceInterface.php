<?php

namespace App\Contracts\Services;

interface ImportServiceInterface
{
    public function importTeams(): array;

    public function importPlayers(?int $teamId = null): array;

    public function importGames(int $season, ?int $teamId = null, bool $playoffs = false): array;

    public function importAll(int $season): array;
}
