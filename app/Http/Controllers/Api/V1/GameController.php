<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\GameServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Game\StoreGameRequest;
use App\Http\Requests\Game\UpdateGameRequest;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GameController extends Controller
{
    use ApiResponse;

    public function __construct(
        private GameServiceInterface $gameService
    ) {}

    public function index(Request $request): JsonResponse
    {
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
        $game = $this->gameService->find($game->id);
        return $this->success(new GameResource($game));
    }

    public function update(UpdateGameRequest $request, Game $game): JsonResponse
    {
        $updated = $this->gameService->update($game->id, $request->validated());
        return $this->success(new GameResource($updated));
    }

    public function destroy(Game $game): Response
    {
        $this->gameService->delete($game->id);
        return $this->noContent();
    }

    public function bySeason(int $season): JsonResponse
    {
        $games = $this->gameService->getBySeason($season);
        return $this->success(GameResource::collection($games));
    }

    public function byDateRange(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $start = Carbon::parse($request->query('start_date'));
        $end = Carbon::parse($request->query('end_date'));

        $games = $this->gameService->getByDateRange($start, $end);
        return $this->success(GameResource::collection($games));
    }
}
