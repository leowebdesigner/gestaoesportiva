<?php

namespace App\Contracts\Repositories;

interface RepositoryInterface extends ReadRepositoryInterface, WriteRepositoryInterface, QueryRepositoryInterface
{
}
