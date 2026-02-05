<?php

namespace Tests\Unit\Repositories;

use App\Models\Game;
use App\Models\Team;
use App\Repositories\GameRepository;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_by_season(): void
    {
        Game::factory()->create(['season' => 2023]);
        Game::factory()->create(['season' => 2022]);

        $repo = new GameRepository(new Game());
        $games = $repo->getBySeason(2023);

        $this->assertCount(1, $games);
    }

    public function test_get_by_team_and_season(): void
    {
        $team = Team::factory()->create();
        $other = Team::factory()->create();

        Game::factory()->create(['home_team_id' => $team->id, 'visitor_team_id' => $other->id, 'season' => 2023]);
        Game::factory()->create(['home_team_id' => $team->id, 'visitor_team_id' => $other->id, 'season' => 2022]);

        $repo = new GameRepository(new Game());
        $games = $repo->getByTeam($team->id, 2023);

        $this->assertCount(1, $games);
    }

    public function test_get_by_date_range(): void
    {
        Game::factory()->create(['game_date' => '2023-01-01']);
        Game::factory()->create(['game_date' => '2024-01-01']);

        $repo = new GameRepository(new Game());
        $games = $repo->getByDateRange(Carbon::parse('2023-01-01'), Carbon::parse('2023-12-31'));

        $this->assertCount(1, $games);
    }
}
