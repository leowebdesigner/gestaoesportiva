<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\PlayerServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Player\StorePlayerRequest;
use App\Http\Requests\Player\UpdatePlayerRequest;
use App\Http\Resources\PlayerResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Player;
use Illuminate\Support\Facades\Gate;

class PlayerController extends Controller
{
    use ApiResponse;

    public function __construct(
        private PlayerServiceInterface $playerService
    ) {}

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', \App\Models\Player::class);
        $players = $this->playerService->list($request->query(), (int) $request->get('per_page', 15));
        return $this->paginated(PlayerResource::collection($players));
    }

    public function store(StorePlayerRequest $request): JsonResponse
    {
        $player = $this->playerService->create($request->validated());
        return $this->created(new PlayerResource($player));
    }

    public function show(Player $player): JsonResponse
    {
        $playerModel = $this->playerService->find($player->id);
        Gate::authorize('view', $playerModel);
        return $this->success(new PlayerResource($playerModel));
    }

    public function update(UpdatePlayerRequest $request, Player $player): JsonResponse
    {
        $playerModel = $this->playerService->update($player->id, $request->validated());
        return $this->success(new PlayerResource($playerModel));
    }

    public function destroy(Player $player): JsonResponse
    {
        $playerModel = $this->playerService->find($player->id);
        Gate::authorize('delete', $playerModel);
        $this->playerService->delete($player->id);
        return $this->noContent();
    }
}
