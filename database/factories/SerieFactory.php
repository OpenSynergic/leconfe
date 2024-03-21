<?php

namespace Database\Factories;

use App\Models\Serie;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

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
        $name = 'Conference';
        $city = fake()->city();
        $year = fake()->year();

        return [
            'title' => "$name $city $year",
            'path' => Str::slug($city),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Serie $series) {
            
        });
    }
}
