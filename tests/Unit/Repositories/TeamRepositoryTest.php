<?php

namespace Tests\Unit\Repositories;

use App\Models\Team;
use App\Repositories\TeamRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_by_external_id(): void
    {
        $team = Team::factory()->create(['external_id' => 77]);
        $repo = new TeamRepository(new Team());

        $found = $repo->findByExternalId(77);

        $this->assertNotNull($found);
        $this->assertEquals($team->id, $found->id);
    }

    public function test_get_by_conference(): void
    {
        Team::factory()->create(['conference' => 'East']);
        Team::factory()->create(['conference' => 'West']);

        $repo = new TeamRepository(new Team());
        $teams = $repo->getByConference('East');

        $this->assertCount(1, $teams);
    }

    public function test_upsert_from_external_creates_and_updates(): void
    {
        $repo = new TeamRepository(new Team());

        $created = $repo->upsertFromExternal([
            'external_id' => 10,
            'name' => 'A',
            'city' => 'X',
            'abbreviation' => 'AX',
            'conference' => 'East',
            'division' => 'Atlantic',
            'full_name' => 'X A',
        ]);

        $updated = $repo->upsertFromExternal([
            'external_id' => 10,
            'name' => 'B',
            'city' => 'X',
            'abbreviation' => 'AX',
            'conference' => 'East',
            'division' => 'Atlantic',
            'full_name' => 'X B',
        ]);

        $this->assertEquals($created->id, $updated->id);
        $this->assertEquals('B', $updated->name);
    }
}
