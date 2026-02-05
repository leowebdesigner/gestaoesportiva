<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\TeamServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Team\StoreTeamRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TeamController extends Controller
{
    use ApiResponse;

    public function __construct(
        private TeamServiceInterface $teamService
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
        return $this->success(new TeamResource($team));
    }

    public function update(UpdateTeamRequest $request, Team $team): JsonResponse
    {
        $updated = $this->teamService->update($team->id, $request->validated());
        return $this->success(new TeamResource($updated));
    }

    public function destroy(Team $team): Response
    {
        $this->teamService->delete($team->id);
        return $this->noContent();
    }

    public function byConference(string $conference): JsonResponse
    {
        $teams = $this->teamService->getByConference($conference);
        return $this->success(TeamResource::collection($teams));
    }

    public function byDivision(string $division): JsonResponse
    {
        $teams = $this->teamService->getByDivision($division);
        return $this->success(TeamResource::collection($teams));
    }
}
