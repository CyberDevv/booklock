<?php

namespace Database\Factories;

use App\Models\Screening;
use App\Models\Screening_Seat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Screening_Seat>
 */
class Screening_SeatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'screening_id' => Screening::factory(),
            'row_label' => fake()->randomElement(range('A', 'J')),
            'seat_number' => fake()->numberBetween(1, 20),
            'is_booked' => false,
        ];
    }

    public function booked(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_booked' => true,
        ]);
    }

    public function unbooked(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_booked' => false,
        ]);
    }
}



