<?php

namespace App\Console\Commands;

use App\Jobs\ImportPlayersJob;
use Illuminate\Console\Command;

class ImportPlayersCommand extends Command
{
    protected $signature = 'import:players {--team=} {--all}';
    protected $description = 'Import players from external API';

    public function handle(): int
    {
        $team = $this->option('team');
        ImportPlayersJob::dispatch($team ? (int) $team : null);
        $this->info('Players import queued.');

        return self::SUCCESS;
    }
}
