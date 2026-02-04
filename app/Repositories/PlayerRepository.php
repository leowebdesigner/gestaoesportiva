<?php

namespace App\Repositories;

use App\Contracts\Repositories\PlayerRepositoryInterface;
use App\Models\Player;
use Illuminate\Support\Collection;

class PlayerRepository extends BaseRepository implements PlayerRepositoryInterface
{
    public function __construct(Player $model)
    {
        parent::__construct($model);
    }

    public function findByExternalId(int $externalId): ?Player
    {
        return $this->model->where('external_id', $externalId)->first();
    }

    public function getByTeam(string $teamId): Collection
    {
        return $this->model->where('team_id', $teamId)->get();
    }

    public function getActiveByPosition(string $position): Collection
    {
        return $this->model->active()->byPosition($position)->get();
    }

    public function searchByName(string $term): Collection
    {
        return $this->model->search($term)->get();
    }

    public function upsertFromExternal(array $data): Player
    {
        return $this->model->updateOrCreate(
            ['external_id' => $data['external_id']],
            $data
        );
    }
}
