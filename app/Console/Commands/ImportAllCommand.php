<?php

namespace App\Console\Commands;

use App\Jobs\ImportGamesJob;
use App\Jobs\ImportPlayersJob;
use App\Jobs\ImportTeamsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ImportAllCommand extends Command
{
    protected $signature = 'import:all {season?}';
    protected $description = 'Importa todos os dados da API externa';

    public function handle(): int
    {
        $season = (int) ($this->argument('season') ?? config('balldontlie.default_season'));

        Bus::chain([
            new ImportTeamsJob(),
            new ImportPlayersJob(),
            new ImportGamesJob($season),
        ])->dispatch();

        $this->info('Import init: Teams → Players → Games.');

        return self::SUCCESS;
    }
}
