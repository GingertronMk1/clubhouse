<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Competition>
 */
class CompetitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $parent = null;
        if (fake()->boolean(20)) {
            if (Competition::query()->count() < 5) {
                $parent = Competition::factory()->create();
            } else {
                $parent = Competition::query()->inRandomOrder()->first();
            }
        }

        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'parent_id' => $parent,
            'location_id' => Location::query()->count() < 5 ? Location::factory() : Location::query()->inRandomOrder()->first(),
        ];
    }
}
