<?php

namespace App\Jobs;

use App\Contracts\Services\ImportServiceInterface;
use App\External\BallDontLie\Contracts\BallDontLieServiceInterface;
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

    public function __construct()
    {
        $this->onQueue('imports');
    }

    public function handle(BallDontLieServiceInterface $service, ImportServiceInterface $importService): void
    {
        $page = 1;
        $perPage = config('balldontlie.pagination.per_page', 100);

        do {
            $response = $service->fetchTeams($page, $perPage);

            $teamsData = array_map(fn($dto) => $dto->toArray(), $response['data']);
            $importService->bulkUpsertTeamsFromExternal($teamsData);

            $page = $response['meta']['next_page'] ?? null;
        } while ($page);
    }
}
