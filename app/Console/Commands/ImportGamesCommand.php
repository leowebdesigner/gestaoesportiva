<?php

namespace App\Console\Commands;

use App\Jobs\ImportGamesJob;
use Illuminate\Console\Command;

class ImportGamesCommand extends Command
{
    protected $signature = 'import:games {season?} {--team=} {--playoffs}';
    protected $description = 'Importa jogos da API externa';

    public function handle(): int
    {
        $season = (int) ($this->argument('season') ?? config('balldontlie.default_season'));
        $team = $this->option('team');
        $playoffs = (bool) $this->option('playoffs');

        ImportGamesJob::dispatch($season, $team ? (int) $team : null, $playoffs);
        $this->info('Importação de jogos iniciada.');

        return self::SUCCESS;
    }
}
