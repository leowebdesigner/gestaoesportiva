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

    public function getBySeason(int $season): Collection;

    public function getByTeam(string $teamId, ?int $season = null): Collection;

    public function getByDateRange(Carbon $start, Carbon $end): Collection;

}
