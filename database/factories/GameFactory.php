<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Sport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
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
        $date = $this->faker->dateTimeBetween('-2 years', '+6 months');
        $summary = null;
        if ($date < now()) {
            $summary = $this->faker->paragraph();
        }

        $competition = Competition::query()->count() < 10
            ? Competition::factory()->create()
            : Competition::query()->inRandomOrder()->first();

        return [
            'name' => $this->faker->words(asText: true),
            'start' => $this->faker->dateTime(),
            'description' => $this->faker->paragraph(),
            'summary' => $summary,
            'competition_id' => $competition->id,
            'score' => $this->generateScore($competition->sport),
        ];
    }

    private function generateScore(Sport $sport): array
    {
        $_ret = [];
        foreach (array_keys($sport->scoring) as $score) {
            $_ret[$score] = $this->faker->numberBetween(0, 25);
        }

        return $_ret;
    }
}
