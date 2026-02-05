<?php

namespace App\Services;

use App\Contracts\Services\ImportServiceInterface;
use App\Jobs\ImportGamesJob;
use App\Jobs\ImportPlayersJob;
use App\Jobs\ImportTeamsJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

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
        Bus::chain([
            new ImportTeamsJob(),
            new ImportPlayersJob($teamId),
        ])->catch(function (\Throwable $e) {
            Log::error('Import players chain failed', [
                'error' => $e->getMessage(),
            ]);
        })->dispatch();

        return [
            'queued' => true,
            'job' => 'import:players',
            'chain' => ['import:teams', 'import:players'],
            'filters' => [
                'team_id' => $teamId,
            ],
        ];
    }

    public function importGames(int $season, ?int $teamId = null, bool $playoffs = false): array
    {
        Bus::chain([
            new ImportTeamsJob(),
            new ImportGamesJob($season, $teamId, $playoffs),
        ])->catch(function (\Throwable $e) use ($season) {
            Log::error('Import games chain failed', [
                'season' => $season,
                'error' => $e->getMessage(),
            ]);
        })->dispatch();

        return [
            'queued' => true,
            'job' => 'import:games',
            'chain' => ['import:teams', 'import:games'],
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
        ])->catch(function (\Throwable $e) use ($season) {
            Log::error('Full import chain failed', [
                'season' => $season,
                'error' => $e->getMessage(),
            ]);
        })->dispatch();

        return [
            'queued' => true,
            'jobs' => ['import:teams', 'import:players', 'import:games'],
            'filters' => [
                'season' => $season,
            ],
        ];
    }
}
