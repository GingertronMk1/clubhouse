<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Club>
 */
class ClubFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $team = Team::query()->count() < 5 ? Team::factory()->create() : Team::query()->inRandomOrder()->first();

        return [
            'name' => $team->name.' '.fake()->randomDigitNotZero().'s',
            'description' => fake()->text(),
            'team_id' => $team,
        ];
    }
}
