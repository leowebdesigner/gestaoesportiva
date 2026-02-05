<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ReadRepositoryInterface
{
    public function all(array $columns = ['*']): Collection;

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function findById(string $id, array $columns = ['*']): ?Model;

    public function findOrFail(string $id, array $columns = ['*']): Model;

    public function exists(string $id): bool;

    public function chunk(int $count, callable $callback): bool;
}
