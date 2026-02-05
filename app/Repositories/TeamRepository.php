<?php

namespace App\Repositories;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Models\Team;
use Illuminate\Support\Collection;

class TeamRepository extends BaseRepository implements TeamRepositoryInterface
{
    public function __construct(Team $model)
    {
        parent::__construct($model);
    }

    public function findByExternalId(int $externalId): ?Team
    {
        return $this->model->where('external_id', $externalId)->first();
    }

    public function getByConference(string $conference): Collection
    {
        return $this->model->byConference($conference)->get();
    }

    public function getByDivision(string $division): Collection
    {
        return $this->model->byDivision($division)->get();
    }

    public function searchByName(string $term): Collection
    {
        return $this->model->search($term)->get();
    }

    public function upsertFromExternal(array $data): Team
    {
        return $this->model->updateOrCreate(
            ['external_id' => $data['external_id']],
            $data
        );
    }

    public function getExternalIdMap(): Collection
    {
        return $this->model->whereNotNull('external_id')
            ->pluck('id', 'external_id');
    }

    public function bulkUpsertFromExternal(array $rows): int
    {
        if (empty($rows)) {
            return 0;
        }

        return $this->model->upsert(
            $rows,
            ['external_id'],
            ['name', 'city', 'abbreviation', 'conference', 'division', 'full_name']
        );
    }
}
