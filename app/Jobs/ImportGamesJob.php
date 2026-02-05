<?php

namespace App\Jobs;

use App\External\BallDontLie\BallDontLieService;
use App\Services\GameService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportGamesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 3600;

    public function __construct(
        private int $season,
        private ?int $teamId = null,
        private bool $playoffs = false
    ) {}

    public function handle(BallDontLieService $service, GameService $gameService): void
    {
        $page = 1;
        $perPage = config('balldontlie.pagination.per_page', 100);

        do {
            $params = [
                'page' => $page,
                'per_page' => $perPage,
                'seasons[]' => $this->season,
                'postseason' => $this->playoffs ? 'true' : 'false',
            ];
            if ($this->teamId) {
                $params['team_ids[]'] = $this->teamId;
            }

            $response = $service->fetchGames($params);
            foreach ($response['data'] as $gameDto) {
                $gameService->importFromExternal($gameDto->toArray());
            }

            $page = $response['meta']['next_page'] ?? null;
        } while ($page);
    }
}
