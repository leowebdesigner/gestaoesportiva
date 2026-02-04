<?php

namespace App\Console\Commands;

use App\Jobs\ImportPlayersJob;
use Illuminate\Console\Command;

class ImportPlayersCommand extends Command
{
    protected $signature = 'import:players {--team=} {--all}';
    protected $description = 'Importa jogadores da API externa';

    public function handle(): int
    {
        $team = $this->option('team');
        ImportPlayersJob::dispatch($team ? (int) $team : null);
        $this->info('Importação de jogadores iniciada.');

        return self::SUCCESS;
    }
}
