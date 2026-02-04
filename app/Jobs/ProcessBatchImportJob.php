<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBatchImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $type,
        private array $payload
    ) {}

    public function handle(): void
    {
        match ($this->type) {
            'teams' => ImportTeamsJob::dispatch(),
            'players' => ImportPlayersJob::dispatch($this->payload['team_id'] ?? null),
            'games' => ImportGamesJob::dispatch(
                $this->payload['season'] ?? config('balldontlie.default_season'),
                $this->payload['team_id'] ?? null,
                $this->payload['playoffs'] ?? false
            ),
            default => null,
        };
    }
}
