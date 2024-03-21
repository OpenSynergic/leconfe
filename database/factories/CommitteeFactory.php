<?php

namespace Database\Factories;

use App\Models\CommitteeRole;
use App\Models\Conference;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Committee>
 */
class CommitteeFactory extends Factory
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
            'committee_role_id' => CommitteeRole::withoutGlobalScopes()->whereConferenceId($randomConference)->pluck('id')->random(),
            'given_name' => fake()->firstName(),
            'family_name' => fake()->lastName(),
            'public_name' => fake()->name(),
            'email' => fake()->email(),
        ];
    }
}
