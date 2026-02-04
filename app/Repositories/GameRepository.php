<?php

namespace App\Repositories;

use App\Contracts\Repositories\GameRepositoryInterface;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GameRepository extends BaseRepository implements GameRepositoryInterface
{
    public function __construct(Game $model)
    {
        parent::__construct($model);
    }

    public function findByExternalId(int $externalId): ?Game
    {
        return $this->model->where('external_id', $externalId)->first();
    }

    public function getBySeason(int $season): Collection
    {
        return $this->model->bySeason($season)->get();
    }

    public function getByTeam(string $teamId, ?int $season = null): Collection
    {
        $query = $this->model->byTeam($teamId);

        if ($season !== null) {
            $query->bySeason($season);
        }

        return $query->get();
    }

    public function getByDateRange(Carbon $start, Carbon $end): Collection
    {
        return $this->model->whereBetween('game_date', [$start, $end])->get();
    }

    public function upsertFromExternal(array $data): Game
    {
        return $this->model->updateOrCreate(
            ['external_id' => $data['external_id']],
            $data
        );
    }
}
