<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    public function definition(): array
    {
        $city = fake()->city();
        $name = fake()->unique()->company();
        $fullName = $city . ' ' . $name;

        return [
            'name' => $name,
            'city' => $city,
            'abbreviation' => strtoupper(fake()->lexify('???')),
            'conference' => fake()->randomElement(['East', 'West']),
            'division' => fake()->word(),
            'full_name' => $fullName,
        ];
    }
}
