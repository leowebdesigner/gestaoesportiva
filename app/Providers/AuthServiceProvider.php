<?php

namespace App\Providers;

use App\Models\Game;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Policies\GamePolicy;
use App\Policies\PlayerPolicy;
use App\Policies\TeamPolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Player::class, PlayerPolicy::class);
        Gate::policy(Team::class, TeamPolicy::class);
        Gate::policy(Game::class, GamePolicy::class);

        Gate::before(function (User $user, string $ability) {
            if ($user->isAdministrator()) {
                return true;
            }

            return null;
        });

        Gate::define('import-data', function (User $user) {
            return $user->isAdministrator()
                ? Response::allow()
                : Response::deny('Apenas administradores podem importar dados.');
        });

        Gate::define('access-admin-panel', function (User $user) {
            return $user->isAdministrator();
        });
    }
}
