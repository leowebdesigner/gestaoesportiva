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
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class ImportService implements ImportServiceInterface
{
    public function __construct(
        private TeamRepositoryInterface $teamRepository,
        private PlayerRepositoryInterface $playerRepository,
        private GameRepositoryInterface $gameRepository
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

    public function getTeamExternalIdMap(): Collection
    {
        return $this->teamRepository->getExternalIdMap();
    }

    public function upsertTeamFromExternal(array $externalData): Team
    {
        $data = [
            'external_id' => $externalData['id'] ?? null,
            'name' => $externalData['name'] ?? null,
            'city' => $externalData['city'] ?? null,
            'abbreviation' => $externalData['abbreviation'] ?? null,
            'conference' => $externalData['conference'] ?? null,
            'division' => $externalData['division'] ?? null,
            'full_name' => $externalData['full_name'] ?? null,
        ];

        return $this->teamRepository->upsertFromExternal($data);
    }

    public function bulkUpsertTeamsFromExternal(array $teamsData): int
    {
        $rows = array_map(fn(array $t) => [
            'external_id' => $t['id'] ?? null,
            'name' => $t['name'] ?? null,
            'city' => $t['city'] ?? null,
            'abbreviation' => $t['abbreviation'] ?? null,
            'conference' => $t['conference'] ?? null,
            'division' => $t['division'] ?? null,
            'full_name' => $t['full_name'] ?? null,
        ], $teamsData);

        return $this->teamRepository->bulkUpsertFromExternal($rows);
    }

    public function upsertPlayerFromExternal(array $externalData, ?Collection $teamMap = null): Player
    {
        $teamId = null;
        if (isset($externalData['team']['id'])) {
            if ($teamMap !== null) {
                $teamId = $teamMap[$externalData['team']['id']] ?? null;
            } else {
                $team = $this->teamRepository->findByExternalId($externalData['team']['id']);
                $teamId = $team?->id;
            }
        }

        $playerData = [
            'external_id' => $externalData['id'] ?? null,
            'first_name' => $externalData['first_name'] ?? null,
            'last_name' => $externalData['last_name'] ?? null,
            'position' => $externalData['position'] ?? null,
            'height' => $externalData['height'] ?? null,
            'weight' => $externalData['weight'] ?? null,
            'jersey_number' => $externalData['jersey_number'] ?? null,
            'college' => $externalData['college'] ?? null,
            'country' => $externalData['country'] ?? null,
            'draft_year' => $externalData['draft_year'] ?? null,
            'draft_round' => $externalData['draft_round'] ?? null,
            'draft_number' => $externalData['draft_number'] ?? null,
            'team_id' => $teamId,
        ];

        return $this->playerRepository->upsertFromExternal($playerData);
    }

    public function bulkUpsertPlayersFromExternal(array $playersData, ?Collection $teamMap = null): int
    {
        if ($teamMap === null) {
            $teamMap = $this->teamRepository->getExternalIdMap();
        }

        $rows = array_map(function (array $p) use ($teamMap) {
            $teamId = null;
            if (isset($p['team']['id'])) {
                $teamId = $teamMap[$p['team']['id']] ?? null;
            }

            return [
                'external_id' => $p['id'] ?? null,
                'first_name' => $p['first_name'] ?? null,
                'last_name' => $p['last_name'] ?? null,
                'position' => $p['position'] ?? null,
                'height' => $p['height'] ?? null,
                'weight' => $p['weight'] ?? null,
                'jersey_number' => $p['jersey_number'] ?? null,
                'college' => $p['college'] ?? null,
                'country' => $p['country'] ?? null,
                'draft_year' => $p['draft_year'] ?? null,
                'draft_round' => $p['draft_round'] ?? null,
                'draft_number' => $p['draft_number'] ?? null,
                'team_id' => $teamId,
            ];
        }, $playersData);

        return $this->playerRepository->bulkUpsertFromExternal($rows);
    }

    public function upsertGameFromExternal(array $externalData, ?Collection $teamMap = null): Game
    {
        $homeTeamId = null;
        $visitorTeamId = null;

        if (isset($externalData['home_team']['id'])) {
            if ($teamMap !== null) {
                $homeTeamId = $teamMap[$externalData['home_team']['id']] ?? null;
            } else {
                $homeTeam = $this->teamRepository->findByExternalId($externalData['home_team']['id']);
                $homeTeamId = $homeTeam?->id;
            }
        }

        if (isset($externalData['visitor_team']['id'])) {
            if ($teamMap !== null) {
                $visitorTeamId = $teamMap[$externalData['visitor_team']['id']] ?? null;
            } else {
                $visitorTeam = $this->teamRepository->findByExternalId($externalData['visitor_team']['id']);
                $visitorTeamId = $visitorTeam?->id;
            }
        }

        $data = [
            'external_id' => $externalData['id'] ?? null,
            'home_team_id' => $homeTeamId,
            'visitor_team_id' => $visitorTeamId,
            'home_team_score' => $externalData['home_team_score'] ?? 0,
            'visitor_team_score' => $externalData['visitor_team_score'] ?? 0,
            'season' => $externalData['season'] ?? null,
            'period' => $externalData['period'] ?? 0,
            'status' => $externalData['status'] ?? null,
            'time' => $externalData['time'] ?? null,
            'postseason' => $externalData['postseason'] ?? false,
            'game_date' => isset($externalData['date']) ? Carbon::parse($externalData['date'])->toDateString() : null,
        ];

        return $this->gameRepository->upsertFromExternal($data);
    }

    public function bulkUpsertGamesFromExternal(array $gamesData, ?Collection $teamMap = null): int
    {
        if ($teamMap === null) {
            $teamMap = $this->teamRepository->getExternalIdMap();
        }

        $rows = array_map(function (array $g) use ($teamMap) {
            $homeTeamId = isset($g['home_team']['id']) ? ($teamMap[$g['home_team']['id']] ?? null) : null;
            $visitorTeamId = isset($g['visitor_team']['id']) ? ($teamMap[$g['visitor_team']['id']] ?? null) : null;

            return [
                'external_id' => $g['id'] ?? null,
                'home_team_id' => $homeTeamId,
                'visitor_team_id' => $visitorTeamId,
                'home_team_score' => $g['home_team_score'] ?? 0,
                'visitor_team_score' => $g['visitor_team_score'] ?? 0,
                'season' => $g['season'] ?? null,
                'period' => $g['period'] ?? 0,
                'status' => $g['status'] ?? null,
                'time' => $g['time'] ?? null,
                'postseason' => $g['postseason'] ?? false,
                'game_date' => isset($g['date']) ? Carbon::parse($g['date'])->toDateString() : null,
            ];
        }, $gamesData);

        return $this->gameRepository->bulkUpsertFromExternal($rows);
    }
}
