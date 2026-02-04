<?php

namespace Tests\Unit\Policies;

use App\Models\Player;
use App\Models\User;
use App\Policies\PlayerPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete(): void
    {
        $admin = User::factory()->admin()->create();
        $player = Player::factory()->create();

        $policy = new PlayerPolicy();

        $this->assertTrue($policy->delete($admin, $player)->allowed());
    }

    public function test_user_cannot_delete(): void
    {
        $user = User::factory()->user()->create();
        $player = Player::factory()->create();

        $policy = new PlayerPolicy();

        $this->assertFalse($policy->delete($user, $player)->allowed());
    }
}
