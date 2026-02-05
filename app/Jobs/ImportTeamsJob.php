<?php

namespace App\Jobs;

use App\External\BallDontLie\BallDontLieService;
use App\Services\TeamService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportTeamsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;
    public int $timeout = 3600;

    public function handle(BallDontLieService $service, TeamService $teamService): void
    {
        $page = 1;
        $perPage = config('balldontlie.pagination.per_page', 100);

        do {
            $response = $service->fetchTeams($page, $perPage);
            foreach ($response['data'] as $teamDto) {
                $teamService->importFromExternal($teamDto->toArray());
            }
            $page = $response['meta']['next_page'] ?? null;
        } while ($page);
    }
}
