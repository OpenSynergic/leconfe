<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Developments\ConferenceSeeder;
use Database\Seeders\Developments\SubmissionSeeder;
use Database\Seeders\Developments\UserSeeder;
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
