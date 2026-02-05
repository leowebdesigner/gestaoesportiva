<?php

namespace App\Repositories;

use App\Contracts\Repositories\GameRepositoryInterface;
use App\Models\Game;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class GameRepository extends BaseRepository implements GameRepositoryInterface
{
    protected array $allowedFilters = [
        'season',
        'team_id',
        'postseason',
        'status',
    ];
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
        return $this->model->with(['homeTeam', 'visitorTeam'])->bySeason($season)->get();
    }

    public function getByTeam(string $teamId, ?int $season = null): Collection
    {
        $query = $this->model->with(['homeTeam', 'visitorTeam'])->byTeam($teamId);

        if ($season !== null) {
            $query->bySeason($season);
        }

        return $query->get();
    }

    public function getByDateRange(Carbon $start, Carbon $end): Collection
    {
        return $this->model->with(['homeTeam', 'visitorTeam'])->whereBetween('game_date', [$start, $end])->get();
    }

    public function upsertFromExternal(array $data): Game
    {
        return $this->model->updateOrCreate(
            ['external_id' => $data['external_id']],
            $data
        );
    }

    public function bulkUpsertFromExternal(array $rows): int
    {
        if (empty($rows)) {
            return 0;
        }

        return $this->model->upsert(
            $rows,
            ['external_id'],
            ['home_team_id', 'visitor_team_id', 'home_team_score', 'visitor_team_score', 'season', 'period', 'status', 'time', 'postseason', 'game_date']
        );
    }
}
