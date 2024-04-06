<?php

namespace Database\Seeders\Developments;

use App\Models\Conference;
use Illuminate\Database\Seeder;

class ConferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Conference::factory()->count(5)->create();
    }
}
