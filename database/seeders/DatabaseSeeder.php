<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Developments\DevelopmentSeeder;
use Database\Seeders\Productions\ProductionSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (config('app.env') === 'production') {
            $this->call(ProductionSeeder::class);
        } else {
            $this->call(DevelopmentSeeder::class);
        }
    }
}
