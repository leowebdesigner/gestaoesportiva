<?php

namespace App\Services\Mappers;

use Illuminate\Support\Collection;

class ExternalPlayerMapper
{
    /**
     * @param array<string, mixed> $data
     * @param Collection<int, string>|null $teamMap
     * @param callable|null $fallbackResolver
     */
    public function resolveTeamId(array $data, ?Collection $teamMap = null, ?callable $fallbackResolver = null): ?string
    {
        if (!isset($data['team']['id'])) {
            return null;
        }

        $externalId = $data['team']['id'];

        if ($teamMap !== null) {
            return $teamMap[$externalId] ?? null;
        }

        return $fallbackResolver ? $fallbackResolver($externalId) : null;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function map(array $data, ?string $teamId): array
    {
        return [
            'external_id' => $data['id'] ?? null,
            'first_name' => $data['first_name'] ?? null,
            'last_name' => $data['last_name'] ?? null,
            'position' => $data['position'] ?? null,
            'height' => $data['height'] ?? null,
            'weight' => $data['weight'] ?? null,
            'jersey_number' => $data['jersey_number'] ?? null,
            'college' => $data['college'] ?? null,
            'country' => $data['country'] ?? null,
            'draft_year' => $data['draft_year'] ?? null,
            'draft_round' => $data['draft_round'] ?? null,
            'draft_number' => $data['draft_number'] ?? null,
            'team_id' => $teamId,
        ];
    }
}
