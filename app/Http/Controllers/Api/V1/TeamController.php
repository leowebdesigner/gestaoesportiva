<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\PlayerServiceInterface;
use App\Contracts\Services\TeamServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\TeamResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Team;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
    use ApiResponse;

    public function __construct(
        private TeamServiceInterface $teamService,
        private PlayerServiceInterface $playerService
    ) {}

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', \App\Models\Team::class);
        $teams = $this->teamService->list($request->query(), (int) $request->get('per_page', 15));
        return $this->paginated(TeamResource::collection($teams));
    }

    public function store(StoreTeamRequest $request): JsonResponse
    {
        $team = $this->teamService->create($request->validated());
        return $this->created(new TeamResource($team));
    }

    public function show(Team $team): JsonResponse
    {
        $teamModel = $this->teamService->find($team->id);
        Gate::authorize('view', $teamModel);
        return $this->success(new TeamResource($teamModel));
    }

    public function update(UpdateTeamRequest $request, Team $team): JsonResponse
    {
        $teamModel = $this->teamService->update($team->id, $request->validated());
        return $this->success(new TeamResource($teamModel));
    }

    public function destroy(Team $team): JsonResponse
    {
        $teamModel = $this->teamService->find($team->id);
        Gate::authorize('delete', $teamModel);
        $this->teamService->delete($team->id);
        return $this->noContent();
    }

    public function players(Team $team): JsonResponse
    {
        $players = $this->playerService->getByTeam($team->id);
        return $this->success(PlayerResource::collection($players));
    }
}
