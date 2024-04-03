<?php

namespace Database\Factories;

use App\Models\Proceeding;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proceeding>
 */
class ProceedingFactory extends Factory
{
    protected $model = Proceeding::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'volume' => $this->faker->randomNumber(),
            'number' => $this->faker->randomNumber(),
            'year' => $this->faker->year(),
        ];
    }
}
