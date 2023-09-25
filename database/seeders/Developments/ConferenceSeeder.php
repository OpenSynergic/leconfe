<?php

namespace Database\Seeders\Developments;

use App\Actions\Conferences\ConferenceSetActiveAction;
use App\Models\Conference;
use Illuminate\Database\Seeder;

class ConferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conferences = Conference::factory()->count(5)->create();

        ConferenceSetActiveAction::run($conferences->first());
    }
}
