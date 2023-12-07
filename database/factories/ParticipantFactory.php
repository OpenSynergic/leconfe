<?php

namespace Database\Factories;

use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class ParticipantFactory extends Factory
{
    protected $model = Participant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'given_name' => fake()->firstName(),
            'family_name' => fake()->lastName(),
            'public_name' => fake()->name(),
            'email' => fake()->email(),
            'country' => 'id',
        ];
    }

    /**
     * Configure the model factory.
     */
    // public function configure(): static
    // {
    //     return $this->afterCreating(function (Participant $participant) {
    //         dispatch(fn() => $participant
    //         ->addMediaFromUrl('https://picsum.photos/800')
    //         ->toMediaCollection('photo'));
    //     });
    // }
}
