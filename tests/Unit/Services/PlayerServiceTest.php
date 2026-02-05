<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\PlayerRepositoryInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Exceptions\NotFoundException;
use App\Models\Player;
use App\Models\Team;
use App\Services\PlayerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class PlayerServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_throws_when_not_found(): void
    {
        $playerRepo = Mockery::mock(PlayerRepositoryInterface::class);
        $teamRepo = Mockery::mock(TeamRepositoryInterface::class);

        $playerRepo->shouldReceive('with')->andReturnSelf();
        $playerRepo->shouldReceive('findByUuid')->andReturn(null);

        $service = new PlayerService($playerRepo, $teamRepo);

        $this->expectException(NotFoundException::class);
        $service->find('invalid');
    }

    public function test_get_by_team_returns_collection(): void
    {
        $playerRepo = Mockery::mock(PlayerRepositoryInterface::class);
        $teamRepo = Mockery::mock(TeamRepositoryInterface::class);

        $team = Team::factory()->make();
        $team->id = '01TESTTEAMID';

        $teamRepo->shouldReceive('findByUuid')->andReturn($team);
        $playerRepo->shouldReceive('getByTeam')->with('01TESTTEAMID')->andReturn(new Collection());

        $service = new PlayerService($playerRepo, $teamRepo);

        $result = $service->getByTeam('01TESTTEAMID');

        $this->assertInstanceOf(Collection::class, $result);
    }
}
