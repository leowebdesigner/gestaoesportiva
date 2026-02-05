<?php

namespace App\Services;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Services\TeamServiceInterface;
use App\Exceptions\NotFoundException;
use App\Models\Team;
use App\Traits\Cacheable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeamService implements TeamServiceInterface
{
    use Cacheable;

    public function __construct(
        private TeamRepositoryInterface $teamRepository
    ) {}

    protected function cachePrefix(): string
    {
        return 'teams:';
    }

    protected function cacheTtl(): int
    {
        return (int) config('cache.ttl.teams', 86400);
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $hash = 'list:' . md5(serialize($filters) . $perPage);

        return $this->cacheRemember($hash, function () use ($filters, $perPage) {
            return $this->teamRepository
                ->filter($filters)
                ->paginate($perPage);
        });
    }

    public function find(string $id): Team
    {
        return $this->cacheRemember('id:' . $id, function () use ($id) {
            $team = $this->teamRepository->findById($id);

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

            $this->cacheClearPrefix();

            Log::info('Team created', ['team_id' => $team->id]);

            return $team;
        });
    }

    public function update(string $id, array $data): Team
    {
        return DB::transaction(function () use ($id, $data) {
            $team = $this->teamRepository->findById($id);

            if (!$team) {
                throw new NotFoundException('Time não encontrado.');
            }

            $team = $this->teamRepository->update($team->id, $data);

            $this->cacheForgetItem($id);
            $this->cacheClearPrefix();

            Log::info('Team updated', ['team_id' => $team->id]);

            return $team;
        });
    }

    public function delete(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $team = $this->teamRepository->findById($id);

            if (!$team) {
                throw new NotFoundException('Time não encontrado.');
            }

            $result = $this->teamRepository->delete($team->id);

            $this->cacheForgetItem($id);
            $this->cacheClearPrefix();

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

    public function bulkImportFromExternal(array $teamsData): int
    {
        $rows = array_map(fn(array $t) => [
            'external_id' => $t['id'] ?? null,
            'name' => $t['name'] ?? null,
            'city' => $t['city'] ?? null,
            'abbreviation' => $t['abbreviation'] ?? null,
            'conference' => $t['conference'] ?? null,
            'division' => $t['division'] ?? null,
            'full_name' => $t['full_name'] ?? null,
        ], $teamsData);

        return $this->teamRepository->bulkUpsertFromExternal($rows);
    }

    public function getByConference(string $conference): Collection
    {
        return $this->teamRepository->getByConference($conference);
    }

    public function getByDivision(string $division): Collection
    {
        return $this->teamRepository->getByDivision($division);
    }

    /**
     * Get a map of external IDs to internal IDs.
     */
    public function getExternalIdMap(): Collection
    {
        return $this->teamRepository->getExternalIdMap();
    }
}
