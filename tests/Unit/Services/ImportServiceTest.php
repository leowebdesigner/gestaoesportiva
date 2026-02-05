<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\GameRepositoryInterface;
use App\Contracts\Repositories\PlayerRepositoryInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Models\Game;
use App\Models\Team;
use App\Services\ImportService;
use App\Services\Mappers\ExternalGameMapper;
use App\Services\Mappers\ExternalPlayerMapper;
use App\Services\Mappers\ExternalTeamMapper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ImportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_upsert_team_from_external_calls_repository(): void
    {
        $teamRepo = Mockery::mock(TeamRepositoryInterface::class);
        $playerRepo = Mockery::mock(PlayerRepositoryInterface::class);
        $gameRepo = Mockery::mock(GameRepositoryInterface::class);

        $teamRepo->shouldReceive('upsertFromExternal')
            ->once()
            ->andReturn(Team::factory()->make());

        $service = new ImportService(
            $teamRepo,
            $playerRepo,
            $gameRepo,
            new ExternalTeamMapper(),
            new ExternalPlayerMapper(),
            new ExternalGameMapper()
        );

        $team = $service->upsertTeamFromExternal([
            'id' => 1,
            'name' => 'Lakers',
            'city' => 'Los Angeles',
            'abbreviation' => 'LAL',
            'conference' => 'West',
            'division' => 'Pacific',
            'full_name' => 'Los Angeles Lakers',
        ]);

        $this->assertInstanceOf(Team::class, $team);
    }

    public function test_upsert_game_from_external_calls_repository(): void
    {
        $teamRepo = Mockery::mock(TeamRepositoryInterface::class);
        $playerRepo = Mockery::mock(PlayerRepositoryInterface::class);
        $gameRepo = Mockery::mock(GameRepositoryInterface::class);

        $home = Team::factory()->make();
        $home->id = 'home-ulid';
        $visitor = Team::factory()->make();
        $visitor->id = 'visitor-ulid';

        $teamRepo->shouldReceive('findByExternalId')->with(1)->andReturn($home);
        $teamRepo->shouldReceive('findByExternalId')->with(2)->andReturn($visitor);
        $gameRepo->shouldReceive('upsertFromExternal')->once()->andReturn(Game::factory()->make());

        $service = new ImportService(
            $teamRepo,
            $playerRepo,
            $gameRepo,
            new ExternalTeamMapper(),
            new ExternalPlayerMapper(),
            new ExternalGameMapper()
        );

        $game = $service->upsertGameFromExternal([
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
