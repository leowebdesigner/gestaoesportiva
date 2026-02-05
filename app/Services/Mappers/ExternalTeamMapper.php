<?php

namespace App\Services\Mappers;

class ExternalTeamMapper
{
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function map(array $data): array
    {
        return [
            'external_id' => $data['id'] ?? null,
            'name' => $data['name'] ?? null,
            'city' => $data['city'] ?? null,
            'abbreviation' => $data['abbreviation'] ?? null,
            'conference' => $data['conference'] ?? null,
            'division' => $data['division'] ?? null,
            'full_name' => $data['full_name'] ?? null,
        ];
    }
}
