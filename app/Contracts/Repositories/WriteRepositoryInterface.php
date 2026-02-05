<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;

interface WriteRepositoryInterface
{
    public function create(array $data): Model;

    public function update(string $id, array $data): Model;

    public function delete(string $id): bool;
}
