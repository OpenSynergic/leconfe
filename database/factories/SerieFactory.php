<?php

namespace Database\Factories;

use App\Models\Serie;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Serie>
 */
class SerieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = Carbon::parse(fake()->date());
        return [
            'title' => $date->year,
            'path' => Str::slug($date->year),
            'issn' => fake()->isbn13(),
            'date_start' => $date,
            'date_end' => $date->copy()->addDays(3),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Serie $serie) {
            $serie->setManyMeta([
                'description' => fake()->paragraphs(3, true),
            ]);
        });
    }
}
