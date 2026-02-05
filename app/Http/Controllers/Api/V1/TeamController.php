<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\GameServiceInterface;
use App\Contracts\Services\PlayerServiceInterface;
use App\Contracts\Services\TeamServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\GameResource;
use App\Http\Resources\PlayerResource;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    use ApiResponse;

    public function __construct(
        private TeamServiceInterface $teamService,
        private PlayerServiceInterface $playerService,
        private GameServiceInterface $gameService
    ) {}

    public function index(Request $request): JsonResponse
    {
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
        $team = $this->teamService->find($team->id);
        return $this->success(new TeamResource($team));
    }

    public function update(UpdateTeamRequest $request, Team $team): JsonResponse
    {
        $updated = $this->teamService->update($team->id, $request->validated());
        return $this->success(new TeamResource($updated));
    }

    public function destroy(Team $team): JsonResponse
    {
        $this->teamService->delete($team->id);
        return $this->noContent();
    }

    public function players(Team $team): JsonResponse
    {
        $players = $this->playerService->getByTeam($team->id);
        return $this->success(PlayerResource::collection($players));
    }

    /**
     * Get teams by conference.
     */
    public function byConference(string $conference): JsonResponse
    {
        $teams = $this->teamService->getByConference($conference);
        return $this->success(TeamResource::collection($teams));
    }

    /**
     * Get teams by division.
     */
    public function byDivision(string $division): JsonResponse
    {
        $teams = $this->teamService->getByDivision($division);
        return $this->success(TeamResource::collection($teams));
    }

    /**
     * Get all games for a team.
     */
    public function games(Team $team, Request $request): JsonResponse
    {
        $season = $request->query('season') ? (int) $request->query('season') : null;
        $games = $this->gameService->getByTeam($team->id, $season);
        return $this->success(GameResource::collection($games));
    }
}
