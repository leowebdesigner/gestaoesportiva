<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\GameServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Game\StoreGameRequest;
use App\Http\Requests\Game\UpdateGameRequest;
use App\Http\Resources\GameResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Game;
use Illuminate\Support\Facades\Gate;

class GameController extends Controller
{
    use ApiResponse;

    public function __construct(
        private GameServiceInterface $gameService
    ) {}

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', \App\Models\Game::class);
        $games = $this->gameService->list($request->query(), (int) $request->get('per_page', 15));
        return $this->paginated(GameResource::collection($games));
    }

    public function store(StoreGameRequest $request): JsonResponse
    {
        $game = $this->gameService->create($request->validated());
        return $this->created(new GameResource($game));
    }

    public function show(Game $game): JsonResponse
    {
        $gameModel = $this->gameService->find($game->id);
        Gate::authorize('view', $gameModel);
        return $this->success(new GameResource($gameModel));
    }

    public function update(UpdateGameRequest $request, Game $game): JsonResponse
    {
        $gameModel = $this->gameService->update($game->id, $request->validated());
        return $this->success(new GameResource($gameModel));
    }

    public function destroy(Game $game): JsonResponse
    {
        $gameModel = $this->gameService->find($game->id);
        Gate::authorize('delete', $gameModel);
        $this->gameService->delete($game->id);
        return $this->noContent();
    }
}
