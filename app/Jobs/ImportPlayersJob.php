<?php

namespace App\Jobs;

use App\External\BallDontLie\BallDontLieService;
use App\Services\PlayerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPlayersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private ?int $teamId = null
    ) {}

    public function handle(BallDontLieService $service, PlayerService $playerService): void
    {
        $page = 1;
        $perPage = config('balldontlie.pagination.per_page', 100);

        do {
            $params = [
                'page' => $page,
                'per_page' => $perPage,
            ];
            if ($this->teamId) {
                $params['team_ids[]'] = $this->teamId;
            }

            $response = $service->fetchPlayers($params);
            foreach ($response['data'] as $playerDto) {
                $playerService->importFromExternal($playerDto->toArray());
            }

            $page = $response['meta']['next_page'] ?? null;
        } while ($page);
    }
}
