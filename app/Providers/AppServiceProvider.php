<?php

namespace App\Providers;

use App\Contracts\Services\AuthServiceInterface;
use App\Contracts\Services\GameServiceInterface;
use App\Contracts\Services\ImportServiceInterface;
use App\Contracts\Services\PlayerServiceInterface;
use App\Contracts\Services\TeamServiceInterface;
use App\Services\AuthService;
use App\Services\GameService;
use App\Services\ImportService;
use App\Services\PlayerService;
use App\Services\TeamService;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Support\ServiceProvider;
use App\Exceptions\Handler;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ExceptionHandlerContract::class, Handler::class);

        // Services (stateless, singleton for performance)
        $this->app->singleton(PlayerServiceInterface::class, PlayerService::class);
        $this->app->singleton(TeamServiceInterface::class, TeamService::class);
        $this->app->singleton(GameServiceInterface::class, GameService::class);
        $this->app->singleton(AuthServiceInterface::class, AuthService::class);
        $this->app->singleton(ImportServiceInterface::class, ImportService::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
