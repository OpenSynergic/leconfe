<?php

namespace Database\Seeders\Developments;

use App\Models\Conference;
use App\Models\Proceeding;
use App\Models\Submission;
use Illuminate\Database\Seeder;

class ProceedingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Conference::lazy()->each(function (Conference $conference) {
            Proceeding::factory()->count(5)->for($conference)->create();
        });
    }
}
