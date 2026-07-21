<?php

namespace Database\Factories;

use App\Models\Hall;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hall>
 */
class HallFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->unique()->word(),
            "rows" => fake()->numberBetween(5, 20),
            "seats_per_row" => fake()->numberBetween(10, 30),
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }
}
