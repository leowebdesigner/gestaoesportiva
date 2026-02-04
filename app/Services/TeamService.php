<?php

namespace App\Services;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Services\TeamServiceInterface;
use App\Exceptions\NotFoundException;
use App\Models\Team;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeamService implements TeamServiceInterface
{
    private const CACHE_TTL = 3600;
    private const CACHE_PREFIX = 'teams:';

    public function __construct(
        private TeamRepositoryInterface $teamRepository
    ) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = self::CACHE_PREFIX . 'list:' . md5(serialize($filters) . $perPage);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filters, $perPage) {
            return $this->teamRepository
                ->filter($filters)
                ->paginate($perPage);
        });
    }

    public function find(string $id): Team
    {
        $cacheKey = self::CACHE_PREFIX . 'id:' . $id;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            $team = $this->teamRepository->findByUuid($id);

            if (!$team) {
                throw new NotFoundException('Time não encontrado.');
            }

            return $team;
        });
    }

    public function create(array $data): Team
    {
        return DB::transaction(function () use ($data) {
            $team = $this->teamRepository->create($data);

            $this->clearListCache();

            Log::info('Team created', ['team_id' => $team->id]);

            return $team;
        });
    }

    public function update(string $id, array $data): Team
    {
        return DB::transaction(function () use ($id, $data) {
            $team = $this->teamRepository->findByUuid($id);

            if (!$team) {
                throw new NotFoundException('Time não encontrado.');
            }

            $team = $this->teamRepository->update($team->id, $data);

            $this->clearTeamCache($id);
            $this->clearListCache();

            Log::info('Team updated', ['team_id' => $team->id]);

            return $team;
        });
    }

    public function delete(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $team = $this->teamRepository->findByUuid($id);

            if (!$team) {
                throw new NotFoundException('Time não encontrado.');
            }

            $result = $this->teamRepository->delete($team->id);

            $this->clearTeamCache($id);
            $this->clearListCache();

            Log::info('Team deleted', ['team_id' => $team->id]);

            return $result;
        });
    }

    public function importFromExternal(array $externalData): Team
    {
        $data = [
            'external_id' => $externalData['id'] ?? null,
            'name' => $externalData['name'] ?? null,
            'city' => $externalData['city'] ?? null,
            'abbreviation' => $externalData['abbreviation'] ?? null,
            'conference' => $externalData['conference'] ?? null,
            'division' => $externalData['division'] ?? null,
            'full_name' => $externalData['full_name'] ?? null,
        ];

        return $this->teamRepository->upsertFromExternal($data);
    }

    public function getByConference(string $conference): Collection
    {
        return $this->teamRepository->getByConference($conference);
    }

    public function getByDivision(string $division): Collection
    {
        return $this->teamRepository->getByDivision($division);
    }

    private function clearTeamCache(string $id): void
    {
        Cache::forget(self::CACHE_PREFIX . 'id:' . $id);
    }

    private function clearListCache(): void
    {
        Cache::flush();
    }
}
