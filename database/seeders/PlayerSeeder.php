<?php

namespace Database\Seeders;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        $teamIds = Team::query()->pluck('id')->all();

        Player::factory()
            ->count(30)
            ->state(function () use ($teamIds) {
                return [
                    'team_id' => !empty($teamIds) ? fake()->randomElement($teamIds) : null,
                ];
            })
            ->create();
    }
}
