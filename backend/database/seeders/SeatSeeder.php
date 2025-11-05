<?php

namespace Database\Seeders;

use App\Models\Seat;
use Illuminate\Database\Seeder;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 9; $i++) {
            for ($j = 1; $j <= 10; $j++) {
                Seat::create([
                    'hall_id' => 1,
                    'seat_name' => $i.'-'.$j,
                    'row' => $i,
                    'column' => $j,
                    'price' => 500,
                ]);
            }
        }
        for ($i = 1; $i <= 7; $i++) {
            for ($j = 1; $j <= 7; $j++) {
                Seat::create([
                    'hall_id' => 2,
                    'seat_name' => $i.'-'.$j,
                    'row' => $i,
                    'column' => $j,
                    'price' => 400,
                ]);
            }
        }
    }
}
