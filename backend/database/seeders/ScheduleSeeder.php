<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schedule::create([
            'movie_id' => 1,
            'hall_id' => 1,
            'date' => '2025-11-10',
            'start_time' => '10:00:00',
        ]);
        Schedule::create([
            'movie_id' => 2,
            'hall_id' => 2,
            'date' => '2025-11-11',
            'start_time' => '14:00:00',
        ]);
    }
}
