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
        $page = (int) ($filters['page'] ?? 1);
        $hash = 'list:' . md5(serialize($filters) . $perPage . $page);

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
                throw new NotFoundException(__('messages.team.not_found'));
            }

            return $team;
        });
    }

    public function create(array $data): Team
    {
        $team = DB::transaction(function () use ($data) {
            return $this->teamRepository->create($data);
        });

        $this->cacheClearPrefix();
        Log::info('Team created', ['team_id' => $team->id]);

        return $team;
    }

    public function update(string $id, array $data): Team
    {
        $team = $this->teamRepository->findById($id);

        if (!$team) {
            throw new NotFoundException(__('messages.team.not_found'));
        }

        $team = DB::transaction(function () use ($team, $data) {
            $team = $this->teamRepository->update($team->id, $data);
            return $team;
        });

        $this->cacheForgetItem($id);
        $this->cacheClearPrefix();
        Log::info('Team updated', ['team_id' => $team->id]);

        return $team;
    }

    public function delete(string $id): bool
    {
        $team = $this->teamRepository->findById($id);

        if (!$team) {
            throw new NotFoundException(__('messages.team.not_found'));
        }

        $result = $this->teamRepository->delete($team->id);

        $this->cacheForgetItem($id);
        $this->cacheClearPrefix();
        Log::info('Team deleted', ['team_id' => $team->id]);

        return $result;
    }

    public function getByConference(string $conference): Collection
    {
        return $this->teamRepository->getByConference($conference);
    }

    public function getByDivision(string $division): Collection
    {
        return $this->teamRepository->getByDivision($division);
    }

}
