<?php

namespace App\Contracts\Repositories;

use App\Models\Team;
use Illuminate\Support\Collection;

interface TeamRepositoryInterface extends RepositoryInterface
{
    public function findByExternalId(int $externalId): ?Team;

    public function getByConference(string $conference): Collection;

    public function getByDivision(string $division): Collection;

    public function searchByName(string $term): Collection;

    public function upsertFromExternal(array $data): Team;

    /**
     * @return Collection<int, Team> keyed by external_id
     */
    public function getExternalIdMap(): Collection;

    public function bulkUpsertFromExternal(array $rows): int;
}
