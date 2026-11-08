<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Game;
use App\Models\Location;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Game> */
class GameFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'start' => fake()->dateTime(),
            'description' => fake()->sentence(),
            'summary' => fake()->sentence(),
            'sport_id' => Sport::factory(),
            'competition_id' => Competition::factory(),
            'club1_id' => Team::factory(),
            'club2_id' => Team::factory(),
            'score' => [],
            'location_id' => Location::factory(),
        ];
    }
}
