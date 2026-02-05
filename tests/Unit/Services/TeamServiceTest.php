<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Exceptions\NotFoundException;
use App\Models\Team;
use App\Services\TeamService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class TeamServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_throws_when_not_found(): void
    {
        $repo = Mockery::mock(TeamRepositoryInterface::class);
        $repo->shouldReceive('findById')->andReturn(null);

        $service = new TeamService($repo);

        $this->expectException(NotFoundException::class);
        $service->find('invalid');
    }

    public function test_import_from_external_calls_upsert(): void
    {
        $repo = Mockery::mock(TeamRepositoryInterface::class);
        $repo->shouldReceive('upsertFromExternal')->once()->andReturn(Team::factory()->make());

        $service = new TeamService($repo);

        $team = $service->importFromExternal([
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

    public function test_get_by_conference_returns_collection(): void
    {
        $repo = Mockery::mock(TeamRepositoryInterface::class);
        $repo->shouldReceive('getByConference')->with('West')->andReturn(new Collection());

        $service = new TeamService($repo);
        $result = $service->getByConference('West');

        $this->assertInstanceOf(Collection::class, $result);
    }
}
