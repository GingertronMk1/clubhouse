<?php

namespace Database\Factories;

use App\Models\Position;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => sprintf('%s %s', fake()->boolean() ? 'Left' : 'Right', fake()->word()),
            'description' => fake()->text(),
            'preview_x' => fake()->numberBetween(1, 100),
            'preview_y' => fake()->numberBetween(1, 100),
            'sort_order' => fake()->randomDigit(),
            'default_number' => fake()->randomDigit(),
            'sport_id' => Sport::query()->count() < 5 ? Sport::factory() : Sport::query()->inRandomOrder()->first(),
            'per_side' => fake()->numberBetween(1, 3),
        ];
    }
}
