<?php

namespace Database\Seeders;

use App\Models\Submission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 100; $i++) {
            Submission::insert(Submission::factory(50000)->make()->toArray());
        }
    }
}
