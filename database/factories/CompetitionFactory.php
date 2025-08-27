<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Competition>
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
        return [
            'name' => $this->faker->company(),
            'description' => $this->faker->text(),
            //            'competition_id' => Competition::query()->count() < 10
            //                ? Competition::factory()->create()->id
            //                : Competition::query()->inRandomOrder()->value('id'),
            'sport_id' => Sport::query()->count() < 10
                ? Sport::factory()->id
                : Sport::query()->inRandomOrder()->value('id'),
        ];
    }
}
