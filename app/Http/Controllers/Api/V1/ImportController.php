<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\Services\ImportServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Import\ImportAllRequest;
use App\Http\Requests\Import\ImportGamesRequest;
use App\Http\Requests\Import\ImportPlayersRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ImportController extends Controller
{
    use ApiResponse;

    public function __construct(
        private ImportServiceInterface $importService
    ) {}

    public function teams(): JsonResponse
    {
        return $this->success($this->importService->importTeams(), 'Teams import queued.', 202);
    }

    public function players(ImportPlayersRequest $request): JsonResponse
    {
        return $this->success(
            $this->importService->importPlayers($request->validated('team_id')),
            'Players import queued.',
            202
        );
    }

    public function games(ImportGamesRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return $this->success(
            $this->importService->importGames(
                (int) ($validated['season'] ?? config('balldontlie.default_season')),
                $validated['team_id'] ?? null,
                (bool) ($validated['playoffs'] ?? false)
            ),
            'Games import queued.',
            202
        );
    }

    public function all(ImportAllRequest $request): JsonResponse
    {
        $season = (int) ($request->validated('season') ?? config('balldontlie.default_season'));

        return $this->success($this->importService->importAll($season), 'Full import queued.', 202);
    }
}
