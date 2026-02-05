<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\GameServiceInterface;
use App\Contracts\Services\PlayerServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Resources\GameResource;
use App\Http\Resources\PlayerResource;
use App\Models\Team;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamRelationsController extends Controller
{
    use ApiResponse;

    public function __construct(
        private PlayerServiceInterface $playerService,
        private GameServiceInterface $gameService
    ) {}

    public function players(Team $team): JsonResponse
    {
        $players = $this->playerService->getByTeam($team->id);
        return $this->success(PlayerResource::collection($players));
    }

    public function games(Team $team, Request $request): JsonResponse
    {
        $season = $request->query('season') ? (int) $request->query('season') : null;
        $games = $this->gameService->getByTeam($team->id, $season);
        return $this->success(GameResource::collection($games));
    }
}
