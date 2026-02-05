<?php

namespace Tests\Unit\Repositories;

use App\Models\Player;
use App\Models\Team;
use App\Repositories\PlayerRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_by_external_id(): void
    {
        $player = Player::factory()->create(['external_id' => 1234]);
        $repo = new PlayerRepository(new Player());

        $found = $repo->findByExternalId(1234);

        $this->assertNotNull($found);
        $this->assertEquals($player->id, $found->id);
    }

    public function test_get_active_by_position(): void
    {
        Player::factory()->create(['position' => 'G', 'is_active' => true]);
        Player::factory()->create(['position' => 'G', 'is_active' => false]);

        $repo = new PlayerRepository(new Player());
        $players = $repo->getActiveByPosition('G');

        $this->assertCount(1, $players);
    }

    public function test_upsert_from_external_creates_and_updates(): void
    {
        $team = Team::factory()->create();
        $repo = new PlayerRepository(new Player());

        $created = $repo->upsertFromExternal([
            'external_id' => 999,
            'first_name' => 'A',
            'last_name' => 'B',
            'team_id' => $team->id,
        ]);

        $updated = $repo->upsertFromExternal([
            'external_id' => 999,
            'first_name' => 'Updated',
            'last_name' => 'B',
            'team_id' => $team->id,
        ]);

        $this->assertEquals($created->id, $updated->id);
        $this->assertEquals('Updated', $updated->first_name);
    }
}
