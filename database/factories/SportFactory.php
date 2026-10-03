<?php

namespace Database\Factories;

use App\Models\Sport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sport>
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
        $scoring = [];
        for ($i = 0; $i < fake()->randomDigitNotZero(); $i++) {
            $scoring[fake()->word()] = fake()->randomDigitNotZero();
        }

        return [
            'name' => sprintf(
                '%s %s %s ball',
                fake()->word(),
                fake()->word(),
                fake()->word()
            ),
            'description' => fake()->text(),
            'scoring' => $scoring,
        ];
    }
}
