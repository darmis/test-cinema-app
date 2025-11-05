<?php

namespace Database\Seeders;

use App\Models\Movie;
use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Movie::create([
            'title' => 'Good movie',
            'description' => 'Good movie about: Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.',
        ]);
        Movie::create([
            'title' => 'Very good movie',
            'description' => 'Very good movie about: LoremLorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt',
        ]);
    }
}
