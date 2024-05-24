<?php

namespace Database\Seeders\Developments;

use App\Models\Conference;
use App\Models\Enums\SerieState;
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
            $date = now()->subYear(5);

            $series = Serie::factory()
                ->count(10)
                ->for($conference)
                ->state(new Sequence(
                    function (Sequence $sequence) use ($conference, $date) {
                        $date->addYear();
                        $now = now();

                        $state = SerieState::Published;
                        
                        if ($date->year < $now->year) {
                            $state = SerieState::Archived;
                        } else if ($date->year > ($now->year + 1) && $date->year < ($now->year + 3)) {
                            $state = SerieState::Draft;
                        } else if ($date->year == $now->year) {
                            $state = SerieState::Current;
                        }

                        return [
                            'title' => $conference->name . ' ' . $date->year,
                            'path' => $date->year,
                            'date_start' => $date->copy(),
                            'date_end' => $date->copy()->addMonth(3),
                            'state' => $state, 
                        ];
                    },
                ))
                ->create();
        });
    }
}
