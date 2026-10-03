<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'address1' => fake()->address(),
            'address2' => fake()->address(),
            'postcode' => fake()->postcode(),
            'city' => fake()->city(),
            'country' => fake()->country(),
        ];
    }
}
