<?php

namespace Database\Factories;

use App\Models\Scoring;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Scoring>
 */
class ScoringFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sport_id' => Sport::factory(),
            'name' => fake()->word(),
            'value' => fake()->numberBetween(0, 10),
        ];
    }
}
