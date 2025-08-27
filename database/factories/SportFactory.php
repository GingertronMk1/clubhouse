<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sport>
 */
class SportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $scores = [];
        for ($i = 0; $i < $this->faker->numberBetween(1, 5); $i++) {
            $scores[$this->faker->word()] = $this->faker->numberBetween(1, 10);
        }

        return [
            'name' => $this->faker->unique()->word().'ball',
            'description' => $this->faker->text(),
            'scoring' => $scores,
        ];
    }
}
