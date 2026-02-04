<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    public function definition(): array
    {
        return [
            'home_team_id' => Team::factory(),
            'visitor_team_id' => Team::factory(),
            'home_team_score' => fake()->numberBetween(80, 130),
            'visitor_team_score' => fake()->numberBetween(80, 130),
            'season' => 2023,
            'period' => fake()->numberBetween(0, 4),
            'status' => fake()->randomElement(['Final', 'In Progress', 'Scheduled']),
            'time' => fake()->optional()->time('H:i'),
            'postseason' => fake()->boolean(20),
            'game_date' => fake()->date(),
        ];
    }
}
