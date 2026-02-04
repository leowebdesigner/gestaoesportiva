<?php

namespace App\Console\Commands;

use App\Jobs\ImportTeamsJob;
use Illuminate\Console\Command;

class ImportTeamsCommand extends Command
{
    protected $signature = 'import:teams';
    protected $description = 'Importa times da API externa';

    public function handle(): int
    {
        ImportTeamsJob::dispatch();
        $this->info('Importação de times iniciada.');

        return self::SUCCESS;
    }
}
