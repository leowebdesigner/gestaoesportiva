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
}
