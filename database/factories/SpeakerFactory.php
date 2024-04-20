<?php

namespace Database\Factories;

use App\Models\Serie;
use App\Models\SpeakerRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Speaker>
 */
class SpeakerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $randomSerie = Serie::pluck('id')->random();
        return [
            'serie_id' => $randomSerie,
            'speaker_role_id' => SpeakerRole::withoutGlobalScopes()->whereSerieId($randomSerie)->pluck('id')->random(),
            'given_name' => fake()->firstName(),
            'family_name' => fake()->lastName(),
            'public_name' => fake()->name(),
            'email' => fake()->email(),
        ];
    }
}
