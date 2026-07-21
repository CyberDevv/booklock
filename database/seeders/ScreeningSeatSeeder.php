<?php

namespace Database\Seeders;

use App\Models\Screening_Seat;
use Illuminate\Database\Seeder;

class ScreeningSeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Screening_Seat::factory(100)->create();
    }
}
