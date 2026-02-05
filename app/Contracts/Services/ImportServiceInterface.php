<?php

namespace App\Contracts\Services;

use App\Models\Game;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Support\Collection;

interface ImportServiceInterface
{
    public function importTeams(): array;

    public function importPlayers(?int $teamId = null): array;

    public function importGames(int $season, ?int $teamId = null, bool $playoffs = false): array;

    public function importAll(int $season): array;

    public function getTeamExternalIdMap(): Collection;

    public function upsertTeamFromExternal(array $externalData): Team;

    /**
     * @param array<int, array<string, mixed>> $teamsData
     */
    public function bulkUpsertTeamsFromExternal(array $teamsData): int;

    public function upsertPlayerFromExternal(array $externalData, ?Collection $teamMap = null): Player;

    /**
     * @param array<int, array<string, mixed>> $playersData
     */
    public function bulkUpsertPlayersFromExternal(array $playersData, ?Collection $teamMap = null): int;

    public function upsertGameFromExternal(array $externalData, ?Collection $teamMap = null): Game;

    /**
     * @param array<int, array<string, mixed>> $gamesData
     */
    public function bulkUpsertGamesFromExternal(array $gamesData, ?Collection $teamMap = null): int;
}
