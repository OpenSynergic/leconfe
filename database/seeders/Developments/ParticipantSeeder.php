<?php

namespace Database\Seeders\Developments;

use App\Models\Participants\Participant;
use Illuminate\Database\Seeder;

class ParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Participant::factory(100)->create();
    }
}
