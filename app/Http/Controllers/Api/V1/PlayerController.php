<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\PlayerServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Player\StorePlayerRequest;
use App\Http\Requests\Player\UpdatePlayerRequest;
use App\Http\Resources\PlayerResource;
use App\Models\Player;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    use ApiResponse;

    public function __construct(
        private PlayerServiceInterface $playerService
    ) {}

    public function index(Request $request): JsonResponse
    {
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
        $player = $this->playerService->find($player->id);
        return $this->success(new PlayerResource($player));
    }

    public function update(UpdatePlayerRequest $request, Player $player): JsonResponse
    {
        $updated = $this->playerService->update($player->id, $request->validated());
        return $this->success(new PlayerResource($updated));
    }

    public function destroy(Player $player): JsonResponse
    {
        $this->playerService->delete($player->id);
        return $this->noContent();
    }
}
