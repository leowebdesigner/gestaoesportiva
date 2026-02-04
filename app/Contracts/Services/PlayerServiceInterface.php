<?php

namespace App\Contracts\Services;

use App\Models\Player;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PlayerServiceInterface
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(string $id): Player;

    public function create(array $data): Player;

    public function update(string $id, array $data): Player;

    public function delete(string $id): bool;

    public function importFromExternal(array $externalData): Player;

    public function getByTeam(string $teamId): Collection;
}
