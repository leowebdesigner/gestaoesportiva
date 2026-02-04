<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\GameController;
use App\Http\Controllers\Api\V1\PlayerController;
use App\Http\Controllers\Api\V1\TeamController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/x-token', [AuthController::class, 'createXToken']);
        Route::delete('/auth/x-token', [AuthController::class, 'revokeXToken']);

        Route::get('/players', [PlayerController::class, 'index'])
            ->middleware('ability:players:read,players:*')
            ->can('viewAny', \App\Models\Player::class);
        Route::post('/players', [PlayerController::class, 'store'])
            ->middleware('ability:players:create,players:*')
            ->can('create', \App\Models\Player::class);
        Route::get('/players/{player}', [PlayerController::class, 'show'])
            ->middleware('ability:players:read,players:*')
            ->can('view', 'player');
        Route::put('/players/{player}', [PlayerController::class, 'update'])
            ->middleware('ability:players:update,players:*')
            ->can('update', 'player');
        Route::delete('/players/{player}', [PlayerController::class, 'destroy'])
            ->middleware('ability:players:delete,players:*')
            ->can('delete', 'player');

        Route::get('/teams', [TeamController::class, 'index'])
            ->middleware('ability:teams:read,teams:*')
            ->can('viewAny', \App\Models\Team::class);
        Route::post('/teams', [TeamController::class, 'store'])
            ->middleware('ability:teams:create,teams:*')
            ->can('create', \App\Models\Team::class);
        Route::get('/teams/{team}', [TeamController::class, 'show'])
            ->middleware('ability:teams:read,teams:*')
            ->can('view', 'team');
        Route::put('/teams/{team}', [TeamController::class, 'update'])
            ->middleware('ability:teams:update,teams:*')
            ->can('update', 'team');
        Route::delete('/teams/{team}', [TeamController::class, 'destroy'])
            ->middleware('ability:teams:delete,teams:*')
            ->can('delete', 'team');
        Route::get('/teams/{team}/players', [TeamController::class, 'players'])
            ->middleware('ability:players:read,players:*')
            ->can('view', 'team');

        Route::get('/games', [GameController::class, 'index'])
            ->middleware('ability:games:read,games:*')
            ->can('viewAny', \App\Models\Game::class);
        Route::post('/games', [GameController::class, 'store'])
            ->middleware('ability:games:create,games:*')
            ->can('create', \App\Models\Game::class);
        Route::get('/games/{game}', [GameController::class, 'show'])
            ->middleware('ability:games:read,games:*')
            ->can('view', 'game');
        Route::put('/games/{game}', [GameController::class, 'update'])
            ->middleware('ability:games:update,games:*')
            ->can('update', 'game');
        Route::delete('/games/{game}', [GameController::class, 'destroy'])
            ->middleware('ability:games:delete,games:*')
            ->can('delete', 'game');
    });
});
