<?php

namespace Tests\Unit\External;

use App\External\BallDontLie\BallDontLieService;
use App\External\BallDontLie\Contracts\BallDontLieClientInterface;
use App\External\BallDontLie\DTOs\TeamDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class BallDontLieServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_teams_maps_to_dto(): void
    {
        $client = Mockery::mock(BallDontLieClientInterface::class);
        $client->shouldReceive('getTeams')->once()->andReturn([
            'data' => [[
                'id' => 1,
                'name' => 'Lakers',
                'city' => 'Los Angeles',
                'abbreviation' => 'LAL',
                'conference' => 'West',
                'division' => 'Pacific',
                'full_name' => 'Los Angeles Lakers',
            ]],
            'meta' => ['next_page' => null],
        ]);

        $service = new BallDontLieService($client);
        $result = $service->fetchTeams();

        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(TeamDTO::class, $result['data'][0]);
    }
}
