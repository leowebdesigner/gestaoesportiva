<?php

namespace App\Console\Commands;

use App\Jobs\ImportTeamsJob;
use Illuminate\Console\Command;

class ImportTeamsCommand extends Command
{
    protected $signature = 'import:teams';
    protected $description = 'Import teams from external API';

    public function handle(): int
    {
        ImportTeamsJob::dispatch();
        $this->info('Teams import queued.');

        return self::SUCCESS;
    }
}
