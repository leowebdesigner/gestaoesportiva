<?php

namespace App\Contracts\Repositories;

use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface GameRepositoryInterface extends RepositoryInterface
{
    public function findByExternalId(int $externalId): ?Game;

    public function getBySeason(int $season): Collection;

    public function getByTeam(string $teamId, ?int $season = null): Collection;

    public function getByDateRange(Carbon $start, Carbon $end): Collection;

    public function upsertFromExternal(array $data): Game;

    public function bulkUpsertFromExternal(array $rows): int;
}
