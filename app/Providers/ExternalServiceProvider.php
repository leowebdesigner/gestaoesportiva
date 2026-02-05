<?php

namespace App\Providers;

use App\External\BallDontLie\BallDontLieClient;
use App\External\BallDontLie\BallDontLieService;
use App\External\BallDontLie\Contracts\BallDontLieClientInterface;
use App\External\BallDontLie\Contracts\BallDontLieServiceInterface;
use Illuminate\Support\ServiceProvider;

class ExternalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BallDontLieClientInterface::class, BallDontLieClient::class);
        $this->app->bind(BallDontLieServiceInterface::class, BallDontLieService::class);
    }

    public function boot(): void
    {
        //
    }
}
