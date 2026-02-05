<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\GameRepositoryInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Exceptions\BusinessException;
use App\Models\Game;
use App\Models\Team;
use App\Services\GameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class GameServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_throws_when_home_team_not_found(): void
    {
        $gameRepo = Mockery::mock(GameRepositoryInterface::class);
        $teamRepo = Mockery::mock(TeamRepositoryInterface::class);

        $teamRepo->shouldReceive('findById')->with('home-id')->andReturn(null);

        $service = new GameService($gameRepo, $teamRepo);

        $this->expectException(BusinessException::class);

        $service->create([
            'home_team_id' => 'home-id',
            'visitor_team_id' => 'visitor-id',
            'season' => 2023,
            'status' => 'Scheduled',
            'game_date' => now()->toDateString(),
        ]);
    }

    public function test_get_by_team_returns_collection(): void
    {
        $gameRepo = Mockery::mock(GameRepositoryInterface::class);
        $teamRepo = Mockery::mock(TeamRepositoryInterface::class);

        $gameRepo->shouldReceive('getByTeam')->with('team-id', 2023)->andReturn(new Collection());

        $service = new GameService($gameRepo, $teamRepo);
        $result = $service->getByTeam('team-id', 2023);

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_import_from_external_calls_upsert(): void
    {
        $gameRepo = Mockery::mock(GameRepositoryInterface::class);
        $teamRepo = Mockery::mock(TeamRepositoryInterface::class);

        $home = Team::factory()->make();
        $home->id = 'home-ulid';
        $visitor = Team::factory()->make();
        $visitor->id = 'visitor-ulid';

        $teamRepo->shouldReceive('findByExternalId')->with(1)->andReturn($home);
        $teamRepo->shouldReceive('findByExternalId')->with(2)->andReturn($visitor);
        $gameRepo->shouldReceive('upsertFromExternal')->once()->andReturn(Game::factory()->make());

        $service = new GameService($gameRepo, $teamRepo);

        $game = $service->importFromExternal([
            'id' => 10,
            'home_team' => ['id' => 1],
            'visitor_team' => ['id' => 2],
            'home_team_score' => 100,
            'visitor_team_score' => 98,
            'season' => 2023,
            'period' => 4,
            'status' => 'Final',
            'time' => null,
            'postseason' => false,
            'date' => '2023-01-01T00:00:00.000Z',
        ]);

        $this->assertInstanceOf(Game::class, $game);
    }
}
