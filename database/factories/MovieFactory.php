<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(3),
            'poster_url' => fake()->imageUrl(640, 480, 'movies', true),
            'genre' => fake()->randomElement(['Action', 'Comedy', 'Drama', 'Horror', 'Romance', 'Sci-Fi']),
            'duration_mins' => fake()->numberBetween(80, 180),
            'age_rating' => fake()->randomElement(['G', 'PG', 'PG-13', 'R', 'NC-17']),
            'is_active' => fake()->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
