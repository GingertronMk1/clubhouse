<?php

namespace Database\Factories;

use App\Models\Sport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Position>
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
            'name' => $this->faker->words(asText: true),
            'description' => $this->faker->text(),
            'preview_x' => $this->faker->numberBetween(1, 100),
            'preview_y' => $this->faker->numberBetween(1, 100),
            'order' => $this->faker->numberBetween(1, 100),
            'default_number' => $this->faker->numberBetween(1, 100),
            'sport_id' => Sport::query()->count() < 10 ? Sport::factory()->create()->id : Sport::query()->inRandomOrder()->value('id'),
        ];
    }
}
