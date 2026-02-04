<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Player>
 */
class PlayerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'position' => fake()->randomElement(['G', 'F', 'C', 'G-F', 'F-C']),
            'height' => fake()->randomElement(['6-2', '6-7', '7-0']),
            'weight' => fake()->randomElement(['190', '210', '250']),
            'jersey_number' => (string) fake()->numberBetween(0, 99),
            'college' => fake()->company(),
            'country' => fake()->country(),
            'draft_year' => fake()->numberBetween(1990, 2023),
            'draft_round' => fake()->numberBetween(1, 2),
            'draft_number' => fake()->numberBetween(1, 60),
            'is_active' => true,
        ];
    }
}
