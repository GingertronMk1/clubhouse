<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Game;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = fake()->dateTime();
        $club1 = Club::query()->count() < 5 ? Club::factory()->create() : Club::query()->inRandomOrder()->first();
        $club2 = Club::query()->count() < 5 ? Club::factory()->create() : Club::query()->whereNot('id', $club1->id)->inRandomOrder()->first();
        $competition = Competition::query()->count() < 5 ? Competition::factory()->create() : Competition::query()->inRandomOrder()->first();

        return [
            'name' => fake()->words(3, true),
            'start' => fake()->dateTime(),
            'description' => fake()->text(),
            'summary' => $start > now() ? null : fake()->text(),
            'sport_id' => Sport::query()->count() < 5 ? Sport::factory() : Sport::query()->inRandomOrder()->first(),
            'competition_id' => $competition,
            'club1_id' => $club1,
            'club2_id' => $club2,
            'location_id' => $competition->location,
        ];
    }
}
