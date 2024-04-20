<?php

namespace Database\Seeders\Developments;

use App\Models\Committee;
use Illuminate\Database\Seeder;

class CommitteeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Committee::factory(300)->create();
    }
}
