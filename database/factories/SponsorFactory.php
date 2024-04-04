<?php

namespace Database\Factories;

use App\Models\Conference;
use App\Models\Sponsor;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SponsorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Sponsor::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Sponsor $model) {
            $placeholder = base64_encode(file_get_contents(resource_path('assets/images/placeholder-vertical.jpg')));
            $model
                ->addMediaFromBase64($placeholder)
                ->usingFileName('logo.jpg')
                ->toMediaCollection('logo');
        });
    }
}
