<?php

namespace App\Contracts\Services;

use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TeamServiceInterface
{
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(string $id): Team;

    public function create(array $data): Team;

    public function update(string $id, array $data): Team;

    public function delete(string $id): bool;

    public function getByConference(string $conference): Collection;

    public function getByDivision(string $division): Collection;

}
