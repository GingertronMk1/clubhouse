<?php

namespace Database\Factories;

use App\Models\Position;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Position> */
class PositionFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'sport_id' => Sport::factory(),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'preview_x' => fake()->numberBetween(0, 100),
            'preview_y' => fake()->numberBetween(0, 100),
            'sort_order' => 0,
            'default_number' => fake()->numberBetween(1, 99),
        ];
    }
}
