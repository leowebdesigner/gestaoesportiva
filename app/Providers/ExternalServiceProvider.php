<?php

namespace App\Providers;

use App\External\BallDontLie\BallDontLieClient;
use App\External\BallDontLie\Contracts\BallDontLieClientInterface;
use Illuminate\Support\ServiceProvider;

class ExternalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BallDontLieClientInterface::class, BallDontLieClient::class);
    }

    public function boot(): void
    {
        //
    }
}
