<?php

namespace Database\Seeders\Developments;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Productions\ProductionSeeder;
use Illuminate\Database\Seeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ProductionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ConferenceSeeder::class);
        $this->call(SubmissionSeeder::class);
    }
}
