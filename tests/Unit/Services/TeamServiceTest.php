<?php

namespace Tests\Unit\Services;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Exceptions\NotFoundException;
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

    public function test_get_by_conference_returns_collection(): void
    {
        $repo = Mockery::mock(TeamRepositoryInterface::class);
        $repo->shouldReceive('getByConference')->with('West')->andReturn(new Collection());

        $service = new TeamService($repo);
        $result = $service->getByConference('West');

        $this->assertInstanceOf(Collection::class, $result);
    }
}
