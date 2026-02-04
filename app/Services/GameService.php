<?php

namespace App\Services;

use App\Contracts\Repositories\GameRepositoryInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Services\GameServiceInterface;
use App\Exceptions\BusinessException;
use App\Exceptions\NotFoundException;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GameService implements GameServiceInterface
{
    private const CACHE_TTL = 3600;
    private const CACHE_PREFIX = 'games:';

    public function __construct(
        private GameRepositoryInterface $gameRepository,
        private TeamRepositoryInterface $teamRepository
    ) {}

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $cacheKey = self::CACHE_PREFIX . 'list:' . md5(serialize($filters) . $perPage);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filters, $perPage) {
            return $this->gameRepository
                ->with(['homeTeam', 'visitorTeam'])
                ->filter($filters)
                ->paginate($perPage);
        });
    }

    public function find(string $id): Game
    {
        $cacheKey = self::CACHE_PREFIX . 'id:' . $id;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            $game = $this->gameRepository
                ->with(['homeTeam', 'visitorTeam'])
                ->findByUuid($id);

            if (!$game) {
                throw new NotFoundException('Jogo não encontrado.');
            }

            return $game;
        });
    }

    public function create(array $data): Game
    {
        return DB::transaction(function () use ($data) {
            $data = $this->resolveTeamIds($data);

            $game = $this->gameRepository->create($data);

            $this->clearListCache();

            Log::info('Game created', ['game_id' => $game->id]);

            return $game->load(['homeTeam', 'visitorTeam']);
        });
    }

    public function update(string $id, array $data): Game
    {
        return DB::transaction(function () use ($id, $data) {
            $game = $this->gameRepository->findByUuid($id);

            if (!$game) {
                throw new NotFoundException('Jogo não encontrado.');
            }

            $data = $this->resolveTeamIds($data);

            $game = $this->gameRepository->update($game->id, $data);

            $this->clearGameCache($id);
            $this->clearListCache();

            Log::info('Game updated', ['game_id' => $game->id]);

            return $game->load(['homeTeam', 'visitorTeam']);
        });
    }

    public function delete(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $game = $this->gameRepository->findByUuid($id);

            if (!$game) {
                throw new NotFoundException('Jogo não encontrado.');
            }

            $result = $this->gameRepository->delete($game->id);

            $this->clearGameCache($id);
            $this->clearListCache();

            Log::info('Game deleted', ['game_id' => $game->id]);

            return $result;
        });
    }

    public function importFromExternal(array $externalData): Game
    {
        return DB::transaction(function () use ($externalData) {
            $homeTeamId = null;
            $visitorTeamId = null;

            if (isset($externalData['home_team']['id'])) {
                $homeTeam = $this->teamRepository->findByExternalId($externalData['home_team']['id']);
                $homeTeamId = $homeTeam?->id;
            }

            if (isset($externalData['visitor_team']['id'])) {
                $visitorTeam = $this->teamRepository->findByExternalId($externalData['visitor_team']['id']);
                $visitorTeamId = $visitorTeam?->id;
            }

            $data = [
                'external_id' => $externalData['id'] ?? null,
                'home_team_id' => $homeTeamId,
                'visitor_team_id' => $visitorTeamId,
                'home_team_score' => $externalData['home_team_score'] ?? 0,
                'visitor_team_score' => $externalData['visitor_team_score'] ?? 0,
                'season' => $externalData['season'] ?? null,
                'period' => $externalData['period'] ?? 0,
                'status' => $externalData['status'] ?? null,
                'time' => $externalData['time'] ?? null,
                'postseason' => $externalData['postseason'] ?? false,
                'game_date' => isset($externalData['date']) ? Carbon::parse($externalData['date'])->toDateString() : null,
            ];

            return $this->gameRepository->upsertFromExternal($data);
        });
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

    private function resolveTeamIds(array $data): array
    {
        if (isset($data['home_team_id'])) {
            $homeTeam = $this->teamRepository->findByUuid($data['home_team_id']);
            if (!$homeTeam) {
                throw new BusinessException('Time mandante não encontrado.', 'HOME_TEAM_NOT_FOUND');
            }
            $data['home_team_id'] = $homeTeam->id;
        }

        if (isset($data['visitor_team_id'])) {
            $visitorTeam = $this->teamRepository->findByUuid($data['visitor_team_id']);
            if (!$visitorTeam) {
                throw new BusinessException('Time visitante não encontrado.', 'VISITOR_TEAM_NOT_FOUND');
            }
            $data['visitor_team_id'] = $visitorTeam->id;
        }

        return $data;
    }

    private function clearGameCache(string $id): void
    {
        Cache::forget(self::CACHE_PREFIX . 'id:' . $id);
    }

    private function clearListCache(): void
    {
        Cache::flush();
    }
}
