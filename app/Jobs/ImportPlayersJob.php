<?php

namespace App\Jobs;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Services\PlayerServiceInterface;
use App\External\BallDontLie\Contracts\BallDontLieServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPlayersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 3600;

    public function __construct(
        private ?int $teamId = null
    ) {}

    public function handle(
        BallDontLieServiceInterface $service,
        PlayerServiceInterface $playerService,
        TeamRepositoryInterface $teamRepository
    ): void {
        $page = 1;
        $perPage = config('balldontlie.pagination.per_page', 100);
        $teamMap = $teamRepository->getExternalIdMap();

        do {
            $params = [
                'page' => $page,
                'per_page' => $perPage,
            ];
            if ($this->teamId) {
                $params['team_ids[]'] = $this->teamId;
            }

            $response = $service->fetchPlayers($params);

            $playersData = array_map(fn($dto) => $dto->toArray(), $response['data']);
            $playerService->bulkImportFromExternal($playersData, $teamMap);

            $page = $response['meta']['next_page'] ?? null;
        } while ($page);
    }
}
