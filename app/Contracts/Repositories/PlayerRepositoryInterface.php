<?php

namespace App\Contracts\Repositories;

use App\Models\Player;
use Illuminate\Support\Collection;

interface PlayerRepositoryInterface extends RepositoryInterface
{
    public function findByExternalId(int $externalId): ?Player;

    public function getByTeam(string $teamId): Collection;

    public function getActiveByPosition(string $position): Collection;

    public function searchByName(string $term): Collection;

    public function upsertFromExternal(array $data): Player;
}
