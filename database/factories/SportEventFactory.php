<?php

namespace Database\Factories;

use App\Models\Sport;
use App\Models\SportEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SportEvent> */
class SportEventFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'sport_id' => Sport::factory(),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'score' => 0,
        ];
    }
}
