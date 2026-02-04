<?php

namespace App\Providers;

use App\Contracts\Repositories\GameRepositoryInterface;
use App\Contracts\Repositories\PlayerRepositoryInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Repositories\GameRepository;
use App\Repositories\PlayerRepository;
use App\Repositories\TeamRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PlayerRepositoryInterface::class, PlayerRepository::class);
        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(GameRepositoryInterface::class, GameRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
