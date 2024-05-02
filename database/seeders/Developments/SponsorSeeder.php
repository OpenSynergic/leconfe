<?php

namespace Database\Seeders\Developments;

use App\Models\Conference;
use App\Models\Serie;
use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sponsor::factory()->count(10)->create();

        Conference::lazy()->each(function (Conference $conference) {
            Sponsor::factory()->count(10)->for($conference)->create();
        });
    }
}
