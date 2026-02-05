<?php

namespace App\Contracts\Repositories;

interface QueryRepositoryInterface
{
    public function with(array $relations): self;

    public function filter(array $filters): self;
}
