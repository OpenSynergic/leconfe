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
            $series = Serie::factory()
                ->count(10)
                ->for($conference)
                ->state(new Sequence(
                    function(Sequence $sequence) use ($conference){
                        $now = now();
                        $now->addYear($sequence->index);
                        
                        return [
                            'title' => $conference->name . ' ' . $now->year,
                            'path' => $now->year,
                            'date_start' => $now->copy(),
                            'date_end' => $now->copy()->addDays(3),
                        ];
                    },
                ))
                ->create();
            $series
                ->first()
                ->publish()
                ->setAsCurrent();
        });
    }
}
