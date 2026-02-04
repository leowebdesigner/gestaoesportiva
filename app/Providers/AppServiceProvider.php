<?php

namespace App\Providers;

use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\GameServiceInterface;
use App\Contracts\Services\PlayerServiceInterface;
use App\Contracts\Services\TeamServiceInterface;
use App\Services\AuthService;
use App\Services\GameService;
use App\Services\PlayerService;
use App\Services\TeamService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PlayerServiceInterface::class, PlayerService::class);
        $this->app->bind(TeamServiceInterface::class, TeamService::class);
        $this->app->bind(GameServiceInterface::class, GameService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
