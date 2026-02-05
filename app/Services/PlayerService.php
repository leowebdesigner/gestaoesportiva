<?php

namespace App\Services;

use App\Contracts\Repositories\PlayerRepositoryInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Services\PlayerServiceInterface;
use App\Exceptions\BusinessException;
use App\Exceptions\NotFoundException;
use App\Models\Player;
use App\Traits\Cacheable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayerService implements PlayerServiceInterface
{
    use Cacheable;

    public function __construct(
        private PlayerRepositoryInterface $playerRepository,
        private TeamRepositoryInterface $teamRepository
    ) {}

    protected function cachePrefix(): string
    {
        return 'players:';
    }

    protected function cacheTtl(): int
    {
        return (int) config('cache.ttl.players', 3600);
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $page = (int) ($filters['page'] ?? 1);
        $hash = 'list:' . md5(serialize($filters) . $perPage . $page);

        return $this->cacheRemember($hash, function () use ($filters, $perPage) {
            return $this->playerRepository
                ->with(['team'])
                ->filter($filters)
                ->paginate($perPage);
        });
    }

    public function find(string $id): Player
    {
        return $this->cacheRemember('id:' . $id, function () use ($id) {
            $player = $this->playerRepository
                ->with(['team'])
                ->findById($id);

            if (!$player) {
                throw new NotFoundException(__('messages.player.not_found'));
            }

            return $player;
        });
    }

    public function create(array $data): Player
    {
        if (isset($data['team_id'])) {
            $team = $this->teamRepository->findById($data['team_id']);
            if (!$team) {
                throw new BusinessException(__('messages.team.not_found'), 'TEAM_NOT_FOUND');
            }
            $data['team_id'] = $team->id;
        }

        $player = DB::transaction(function () use ($data) {
            $player = $this->playerRepository->create($data);
            return $player->load('team');
        });

        $this->cacheClearPrefix();
        Log::info('Player created', ['player_id' => $player->id]);

        return $player;
    }

    public function update(string $id, array $data): Player
    {
        $player = $this->playerRepository->findById($id);

        if (!$player) {
            throw new NotFoundException(__('messages.player.not_found'));
        }

        if (isset($data['team_id'])) {
            $team = $this->teamRepository->findById($data['team_id']);
            if (!$team) {
                throw new BusinessException(__('messages.team.not_found'), 'TEAM_NOT_FOUND');
            }
            $data['team_id'] = $team->id;
        }

        $player = DB::transaction(function () use ($player, $data) {
            $player = $this->playerRepository->update($player->id, $data);
            return $player->load('team');
        });

        $this->cacheForgetItem($id);
        $this->cacheClearPrefix();
        Log::info('Player updated', ['player_id' => $player->id]);

        return $player;
    }

    public function delete(string $id): bool
    {
        $player = $this->playerRepository->findById($id);

        if (!$player) {
            throw new NotFoundException(__('messages.player.not_found'));
        }

        $result = $this->playerRepository->delete($player->id);

        $this->cacheForgetItem($id);
        $this->cacheClearPrefix();
        Log::info('Player deleted', ['player_id' => $player->id]);

        return $result;
    }

    public function getByTeam(string $teamId): Collection
    {
        $players = $this->playerRepository->getByTeam($teamId);

        if ($players->isEmpty() && !$this->teamRepository->exists($teamId)) {
            throw new NotFoundException(__('messages.team.not_found'));
        }

        return $players;
    }

    /**
     * Search players by name.
     */
    public function searchByName(string $term): Collection
    {
        return $this->cacheRemember('search:' . md5($term), function () use ($term) {
            return $this->playerRepository->searchByName($term);
        });
    }
}
