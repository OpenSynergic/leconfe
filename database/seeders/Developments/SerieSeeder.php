<?php

namespace Database\Seeders\Developments;

use App\Models\Conference;
use App\Models\Serie;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class SerieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Conference::lazy()->each(function (Conference $conference) {
            $year = now()->year;
            Serie::factory()
                ->count(10)
                ->for($conference)
                ->state(new Sequence(
                    fn(Sequence $sequence) => ['title' => $conference->name . ' ' . $year + $sequence->index],
                ))
                ->create();

            Serie::where('conference_id', $conference->id)
                ->first()
                ->update([
                    'active' => 1,
                ]);
        });
    }
}
