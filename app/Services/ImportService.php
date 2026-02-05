<?php

namespace App\Services;

use App\Contracts\Repositories\GameRepositoryInterface;
use App\Contracts\Repositories\PlayerRepositoryInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Services\ImportServiceInterface;
use App\Jobs\ImportGamesJob;
use App\Jobs\ImportPlayersJob;
use App\Jobs\ImportTeamsJob;
use App\Models\Game;
use App\Models\Player;
use App\Models\Team;
use App\Services\Mappers\ExternalGameMapper;
use App\Services\Mappers\ExternalPlayerMapper;
use App\Services\Mappers\ExternalTeamMapper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class ImportService implements ImportServiceInterface
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private PlayerRepositoryInterface $playerRepository,
        private GameRepositoryInterface $gameRepository,
        private ExternalTeamMapper $teamMapper,
        private ExternalPlayerMapper $playerMapper,
        private ExternalGameMapper $gameMapper
    ) {}

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
            new ImportGamesJob($season),
            new ImportPlayersJob(),
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

    public function getTeamExternalIdMap(): Collection
    {
        return $this->teamRepository->getExternalIdMap();
    }

    public function upsertTeamFromExternal(array $externalData): Team
    {
        return $this->teamRepository->upsertFromExternal(
            $this->teamMapper->map($externalData)
        );
    }

    public function bulkUpsertTeamsFromExternal(array $teamsData): int
    {
        $total = 0;

        foreach (array_chunk($teamsData, 500) as $chunk) {
            $rows = array_map(
                fn(array $t) => $this->teamMapper->map($t),
                $chunk
            );
            $total += $this->teamRepository->bulkUpsertFromExternal($rows);
        }

        return $total;
    }

    public function upsertPlayerFromExternal(array $externalData, ?Collection $teamMap = null): Player
    {
        $teamId = $this->playerMapper->resolveTeamId(
            $externalData,
            $teamMap,
            fn (int $externalId) => $this->teamRepository->findByExternalId($externalId)?->id
        );

        return $this->playerRepository->upsertFromExternal(
            $this->playerMapper->map($externalData, $teamId)
        );
    }

    public function bulkUpsertPlayersFromExternal(array $playersData, ?Collection $teamMap = null): int
    {
        if ($teamMap === null) {
            $teamMap = $this->teamRepository->getExternalIdMap();
        }

        $total = 0;

        foreach (array_chunk($playersData, 500) as $chunk) {
            $rows = array_map(function (array $p) use ($teamMap) {
                $teamId = $this->playerMapper->resolveTeamId($p, $teamMap);
                return $this->playerMapper->map($p, $teamId);
            }, $chunk);

            $total += $this->playerRepository->bulkUpsertFromExternal($rows);
        }

        return $total;
    }

    public function upsertGameFromExternal(array $externalData, ?Collection $teamMap = null): Game
    {
        $teamIds = $this->gameMapper->resolveTeamIds(
            $externalData,
            $teamMap,
            fn (int $externalId) => $this->teamRepository->findByExternalId($externalId)?->id
        );

        return $this->gameRepository->upsertFromExternal(
            $this->gameMapper->map($externalData, $teamIds['home'], $teamIds['visitor'])
        );
    }

    public function bulkUpsertGamesFromExternal(array $gamesData, ?Collection $teamMap = null): int
    {
        if ($teamMap === null) {
            $teamMap = $this->teamRepository->getExternalIdMap();
        }

        $total = 0;

        foreach (array_chunk($gamesData, 500) as $chunk) {
            $rows = array_map(function (array $g) use ($teamMap) {
                $teamIds = $this->gameMapper->resolveTeamIds($g, $teamMap);
                return $this->gameMapper->map($g, $teamIds['home'], $teamIds['visitor']);
            }, $chunk);

            $total += $this->gameRepository->bulkUpsertFromExternal($rows);
        }

        return $total;
    }
}
