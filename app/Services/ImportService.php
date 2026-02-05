<?php

namespace App\Services;

use App\Contracts\Services\ImportServiceInterface;
use App\Jobs\ImportGamesJob;
use App\Jobs\ImportPlayersJob;
use App\Jobs\ImportTeamsJob;
use Illuminate\Support\Facades\Bus;

class ImportService implements ImportServiceInterface
{
    public function importTeams(): array
    {
        ImportTeamsJob::dispatch();

        return [
            'queued' => true,
            'job' => 'import:teams',
        ];
    }

    public function importPlayers(?int $teamId = null): array
    {
        ImportPlayersJob::dispatch($teamId);

        return [
            'queued' => true,
            'job' => 'import:players',
            'filters' => [
                'team_id' => $teamId,
            ],
        ];
    }

    public function importGames(int $season, ?int $teamId = null, bool $playoffs = false): array
    {
        ImportGamesJob::dispatch($season, $teamId, $playoffs);

        return [
            'queued' => true,
            'job' => 'import:games',
            'filters' => [
                'season' => $season,
                'team_id' => $teamId,
                'playoffs' => $playoffs,
            ],
        ];
    }

    public function importAll(int $season): array
    {
        Bus::chain([
            new ImportTeamsJob(),
            new ImportPlayersJob(),
            new ImportGamesJob($season),
        ])->dispatch();

        return [
            'queued' => true,
            'jobs' => ['import:teams', 'import:players', 'import:games'],
            'filters' => [
                'season' => $season,
            ],
        ];
    }
}
