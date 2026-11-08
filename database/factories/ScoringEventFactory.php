<?php

namespace Database\Factories;

use App\Models\ScoringEvent;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ScoringEvent> */
class ScoringEventFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'sport_id' => Sport::factory(),
            'name' => fake()->word(),
            'value' => fake()->numberBetween(1, 10),
            'sort_order' => 0,
        ];
    }
}
