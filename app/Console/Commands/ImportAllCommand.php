<?php

namespace App\Console\Commands;

use App\Jobs\ImportGamesJob;
use App\Jobs\ImportPlayersJob;
use App\Jobs\ImportTeamsJob;
use Illuminate\Console\Command;

class ImportAllCommand extends Command
{
    protected $signature = 'import:all {season?}';
    protected $description = 'Importa todos os dados da API externa';

    public function handle(): int
    {
        $season = (int) ($this->argument('season') ?? config('balldontlie.default_season'));

        ImportTeamsJob::dispatch();
        ImportPlayersJob::dispatch();
        ImportGamesJob::dispatch($season);

        $this->info('Importação completa iniciada.');

        return self::SUCCESS;
    }
}
