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
        $hash = 'list:' . md5(serialize($filters) . $perPage);

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
                throw new NotFoundException('Jogador não encontrado.');
            }

            return $player;
        });
    }

    public function create(array $data): Player
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['team_id'])) {
                $team = $this->teamRepository->findById($data['team_id']);
                if (!$team) {
                    throw new BusinessException('Time não encontrado.', 'TEAM_NOT_FOUND');
                }
                $data['team_id'] = $team->id;
            }

            $player = $this->playerRepository->create($data);

            $this->cacheClearPrefix();

            Log::info('Player created', ['player_id' => $player->id]);

            return $player->load('team');
        });
    }

    public function update(string $id, array $data): Player
    {
        return DB::transaction(function () use ($id, $data) {
            $player = $this->playerRepository->findById($id);

            if (!$player) {
                throw new NotFoundException('Jogador não encontrado.');
            }

            if (isset($data['team_id'])) {
                $team = $this->teamRepository->findById($data['team_id']);
                if (!$team) {
                    throw new BusinessException('Time não encontrado.', 'TEAM_NOT_FOUND');
                }
                $data['team_id'] = $team->id;
            }

            $player = $this->playerRepository->update($player->id, $data);

            $this->cacheForgetItem($id);
            $this->cacheClearPrefix();

            Log::info('Player updated', ['player_id' => $player->id]);

            return $player->load('team');
        });
    }

    public function delete(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $player = $this->playerRepository->findById($id);

            if (!$player) {
                throw new NotFoundException('Jogador não encontrado.');
            }

            $result = $this->playerRepository->delete($player->id);

            $this->cacheForgetItem($id);
            $this->cacheClearPrefix();

            Log::info('Player deleted', ['player_id' => $player->id]);

            return $result;
        });
    }

    public function importFromExternal(array $externalData): Player
    {
        return DB::transaction(function () use ($externalData) {
            $teamId = null;
            if (isset($externalData['team']['id'])) {
                $team = $this->teamRepository->findByExternalId($externalData['team']['id']);
                $teamId = $team?->id;
            }

            $playerData = [
                'external_id' => $externalData['id'] ?? null,
                'first_name' => $externalData['first_name'] ?? null,
                'last_name' => $externalData['last_name'] ?? null,
                'position' => $externalData['position'] ?? null,
                'height' => $externalData['height'] ?? null,
                'weight' => $externalData['weight'] ?? null,
                'jersey_number' => $externalData['jersey_number'] ?? null,
                'college' => $externalData['college'] ?? null,
                'country' => $externalData['country'] ?? null,
                'draft_year' => $externalData['draft_year'] ?? null,
                'draft_round' => $externalData['draft_round'] ?? null,
                'draft_number' => $externalData['draft_number'] ?? null,
                'team_id' => $teamId,
            ];

            return $this->playerRepository->upsertFromExternal($playerData);
        });
    }

    public function getByTeam(string $teamId): Collection
    {
        $team = $this->teamRepository->findById($teamId);

        if (!$team) {
            throw new NotFoundException('Time não encontrado.');
        }

        return $this->playerRepository->getByTeam($team->id);
    }
}
