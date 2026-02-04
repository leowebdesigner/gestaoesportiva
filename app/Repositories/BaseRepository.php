<?php

namespace App\Repositories;

use App\Contracts\Repositories\RepositoryInterface;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseRepository implements RepositoryInterface
{
    use Filterable;

    protected Model $model;
    protected ?Builder $query = null;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->newQuery()->get($columns);
    }

    public function paginate(int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return $this->newQuery()->paginate($perPage, $columns);
    }

    public function find(string $id, array $columns = ['*']): ?Model
    {
        return $this->newQuery()->find($id, $columns);
    }

    public function findByUuid(string $uuid, array $columns = ['*']): ?Model
    {
        return $this->newQuery()->where('id', $uuid)->first($columns);
    }

    public function findOrFail(string $id, array $columns = ['*']): Model
    {
        return $this->newQuery()->findOrFail($id, $columns);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): Model
    {
        $model = $this->findOrFail($id);
        $model->fill($data);
        $model->save();

        return $model;
    }

    public function delete(string $id): bool
    {
        $model = $this->findOrFail($id);
        return (bool) $model->delete();
    }

    public function exists(string $id): bool
    {
        return $this->newQuery()->whereKey($id)->exists();
    }

    public function with(array $relations): self
    {
        $this->query = ($this->query ?? $this->model->newQuery())->with($relations);
        return $this;
    }

    public function filter(array $filters): self
    {
        $this->query = $this->applyFilters($this->query ?? $this->model->newQuery(), $filters);
        return $this;
    }

    public function chunk(int $count, callable $callback): bool
    {
        return $this->newQuery()->chunk($count, $callback);
    }

    protected function newQuery(): Builder
    {
        $query = $this->query ?? $this->model->newQuery();
        $this->query = null;
        return $query;
    }
}
