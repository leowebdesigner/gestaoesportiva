<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Team;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        $teamIds = Team::query()->pluck('id')->values();

        if ($teamIds->count() < 2) {
            return;
        }

        Game::factory()
            ->count(30)
            ->state(function () use ($teamIds) {
                $homeTeamId = $teamIds->random();
                $visitorTeamId = $teamIds
                    ->reject(fn ($id) => $id === $homeTeamId)
                    ->values()
                    ->random();

                return [
                    'home_team_id' => $homeTeamId,
                    'visitor_team_id' => $visitorTeamId,
                ];
            })
            ->create();
    }
}
