<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Developments\CommitteeSeeder;
use Database\Seeders\Developments\ConferenceSeeder;
use Database\Seeders\Developments\ParticipantSeeder;
use Database\Seeders\Developments\SpeakerSeeder;
use Database\Seeders\Developments\SerieSeeder;
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
        $this->call(ConferenceSeeder::class);
        $this->call(SerieSeeder::class);
        $this->call(SubmissionSeeder::class);
        $this->call(ParticipantSeeder::class);
        $this->call(CommitteeSeeder::class);
        $this->call(SpeakerSeeder::class);
        $this->call(SerieSeeder::class);
        $this->call(UserSeeder::class);
    }
}
