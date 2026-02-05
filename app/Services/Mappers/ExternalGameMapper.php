<?php

namespace App\Services\Mappers;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class ExternalGameMapper
{
    /**
     * @param array<string, mixed> $data
     * @param Collection<int, string>|null $teamMap
     * @param callable|null $fallbackResolver
     * @return array{home: ?string, visitor: ?string}
     */
    public function resolveTeamIds(array $data, ?Collection $teamMap = null, ?callable $fallbackResolver = null): array
    {
        $homeId = null;
        $visitorId = null;

        if (isset($data['home_team']['id'])) {
            $externalId = $data['home_team']['id'];
            if ($teamMap !== null) {
                $homeId = $teamMap[$externalId] ?? null;
            } elseif ($fallbackResolver) {
                $homeId = $fallbackResolver($externalId);
            }
        }

        if (isset($data['visitor_team']['id'])) {
            $externalId = $data['visitor_team']['id'];
            if ($teamMap !== null) {
                $visitorId = $teamMap[$externalId] ?? null;
            } elseif ($fallbackResolver) {
                $visitorId = $fallbackResolver($externalId);
            }
        }

        return [
            'home' => $homeId,
            'visitor' => $visitorId,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function map(array $data, ?string $homeTeamId, ?string $visitorTeamId): array
    {
        return [
            'external_id' => $data['id'] ?? null,
            'home_team_id' => $homeTeamId,
            'visitor_team_id' => $visitorTeamId,
            'home_team_score' => $data['home_team_score'] ?? 0,
            'visitor_team_score' => $data['visitor_team_score'] ?? 0,
            'season' => $data['season'] ?? null,
            'period' => $data['period'] ?? 0,
            'status' => $data['status'] ?? null,
            'time' => $data['time'] ?? null,
            'postseason' => $data['postseason'] ?? false,
            'game_date' => isset($data['date']) ? Carbon::parse($data['date'])->toDateString() : null,
        ];
    }
}
