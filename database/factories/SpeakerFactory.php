<?php

namespace Database\Factories;

use App\Models\Conference;
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
        $randomConference = Conference::pluck('id')->random();
        return [
            'conference_id' => $randomConference,
            'speaker_role_id' => SpeakerRole::withoutGlobalScopes()->whereConferenceId($randomConference)->pluck('id')->random(),
            'given_name' => fake()->firstName(),
            'family_name' => fake()->lastName(),
            'public_name' => fake()->name(),
            'email' => fake()->email(),
        ];
    }
}
