<?php

namespace App\External\Contracts;

interface ExternalApiClientInterface
{
    public function get(string $uri, array $query = []): array;
}
