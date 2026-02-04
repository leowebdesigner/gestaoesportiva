<?php

use App\Console\Commands\ImportAllCommand;
use App\Console\Commands\ImportGamesCommand;
use App\Console\Commands\ImportPlayersCommand;
use App\Console\Commands\ImportTeamsCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::starting(function () {
    Artisan::resolve(ImportTeamsCommand::class);
    Artisan::resolve(ImportPlayersCommand::class);
    Artisan::resolve(ImportGamesCommand::class);
    Artisan::resolve(ImportAllCommand::class);
});
