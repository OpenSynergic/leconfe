<?php

namespace Database\Seeders\Developments;

use App\Models\Conference;
use App\Models\Serie;
use App\Models\Sponsor;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Conference::lazy()->each(function (Conference $conference) {
            Topic::factory()->count(10)->for($conference)->create();
        });
    }
}
