<?php

namespace App\Contracts\Services;

use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface GameServiceInterface
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(string $id): Game;

    public function create(array $data): Game;

    public function update(string $id, array $data): Game;

    public function delete(string $id): bool;

    public function importFromExternal(array $externalData): Game;

    public function getBySeason(int $season): Collection;

    public function getByTeam(string $teamId, ?int $season = null): Collection;

    public function getByDateRange(Carbon $start, Carbon $end): Collection;

    /**
     * Bulk import games from external data.
     *
     * @param array<int, array<string, mixed>> $gamesData
     * @param Collection|null $teamMap Pre-fetched team ID map for optimization
     * @return int Number of imported/updated games
     */
    public function bulkImportFromExternal(array $gamesData, ?Collection $teamMap = null): int;
}
