<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface RepositoryInterface
{
    public function all(array $columns = ['*']): Collection;

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function findById(string $id, array $columns = ['*']): ?Model;

    public function findOrFail(string $id, array $columns = ['*']): Model;

    public function create(array $data): Model;

    public function update(string $id, array $data): Model;

    public function delete(string $id): bool;

    public function exists(string $id): bool;

    public function with(array $relations): self;

    public function filter(array $filters): self;

    public function chunk(int $count, callable $callback): bool;
}
