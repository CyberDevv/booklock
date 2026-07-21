<?php

namespace Database\Factories;

use App\Models\Hall;
use App\Models\Movie;
use App\Models\Screening;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Screening>
 */
class ScreeningFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'movie_id' => Movie::factory(),
            'hall_id' => Hall::factory(),
            'starts_at' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'price_kobo' => $this->faker->numberBetween(1000, 5000),
        ];
    }
}
