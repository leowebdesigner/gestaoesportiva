<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\GameRepositoryInterface;
use App\Models\Game;
use App\Services\GameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class GameServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_calls_repository(): void
    {
        $gameRepo = Mockery::mock(GameRepositoryInterface::class);
        $service = new GameService($gameRepo);

        $model = Mockery::mock(Game::class)->makePartial();
        $model->id = 'game-id';
        $model->shouldReceive('load')->with(['homeTeam', 'visitorTeam'])->andReturnSelf();

        $gameRepo->shouldReceive('create')->once()->andReturn($model);

        $result = $service->create([
            'home_team_id' => 'home-id',
            'visitor_team_id' => 'visitor-id',
            'season' => 2023,
            'status' => 'Scheduled',
            'game_date' => now()->toDateString(),
        ]);

        $this->assertSame($model, $result);
    }

    public function test_get_by_team_returns_collection(): void
    {
        $gameRepo = Mockery::mock(GameRepositoryInterface::class);
        $gameRepo->shouldReceive('getByTeam')->with('team-id', 2023)->andReturn(new Collection());

        $service = new GameService($gameRepo);
        $result = $service->getByTeam('team-id', 2023);

        $this->assertInstanceOf(Collection::class, $result);
    }
}
