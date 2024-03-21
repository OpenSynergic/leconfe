<?php

namespace Database\Seeders\Developments;

use App\Models\Conference;
use App\Models\Serie;
use Illuminate\Database\Seeder;

class SerieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Conference::lazy()->each(function (Conference $conference) {
            Serie::factory()->count(10)->for($conference)->create();
        });
    }
}
