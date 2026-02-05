<?php

namespace App\Services;

use App\Contracts\Repositories\GameRepositoryInterface;
use App\Contracts\Services\GameServiceInterface;
use App\Exceptions\NotFoundException;
use App\Models\Game;
use App\Traits\Cacheable;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GameService implements GameServiceInterface
{
    use Cacheable;

    public function __construct(
        private GameRepositoryInterface $gameRepository
    ) {}

    protected function cachePrefix(): string
    {
        return 'games:';
    }

    protected function cacheTtl(): int
    {
        return (int) config('cache.ttl.games', 3600);
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $page = (int) ($filters['page'] ?? 1);
        $hash = 'list:' . md5(serialize($filters) . $perPage . $page);

        return $this->cacheRemember($hash, function () use ($filters, $perPage) {
            return $this->gameRepository
                ->with(['homeTeam', 'visitorTeam'])
                ->filter($filters)
                ->paginate($perPage);
        });
    }

    public function find(string $id): Game
    {
        return $this->cacheRemember('id:' . $id, function () use ($id) {
            $game = $this->gameRepository
                ->with(['homeTeam', 'visitorTeam'])
                ->findById($id);

            if (!$game) {
                throw new NotFoundException(__('messages.game.not_found'));
            }

            return $game;
        });
    }

    public function create(array $data): Game
    {
        $game = DB::transaction(function () use ($data) {
            $game = $this->gameRepository->create($data);
            return $game->load(['homeTeam', 'visitorTeam']);
        });

        $this->cacheClearPrefix();
        Log::info('Game created', ['game_id' => $game->id]);

        return $game;
    }

    public function update(string $id, array $data): Game
    {
        $game = $this->gameRepository->findById($id);

        if (!$game) {
            throw new NotFoundException(__('messages.game.not_found'));
        }

        $game = DB::transaction(function () use ($game, $data) {
            $game = $this->gameRepository->update($game->id, $data);
            return $game->load(['homeTeam', 'visitorTeam']);
        });

        $this->cacheForgetItem($id);
        $this->cacheClearPrefix();
        Log::info('Game updated', ['game_id' => $game->id]);

        return $game;
    }

    public function delete(string $id): bool
    {
        $game = $this->gameRepository->findById($id);

        if (!$game) {
            throw new NotFoundException(__('messages.game.not_found'));
        }

        $result = $this->gameRepository->delete($game->id);

        $this->cacheForgetItem($id);
        $this->cacheClearPrefix();
        Log::info('Game deleted', ['game_id' => $game->id]);

        return $result;
    }

    public function getBySeason(int $season): Collection
    {
        return $this->gameRepository->getBySeason($season);
    }

    public function getByTeam(string $teamId, ?int $season = null): Collection
    {
        return $this->gameRepository->getByTeam($teamId, $season);
    }

    public function getByDateRange(Carbon $start, Carbon $end): Collection
    {
        return $this->gameRepository->getByDateRange($start, $end);
    }

}
