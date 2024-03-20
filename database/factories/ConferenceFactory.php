<?php

namespace Database\Factories;

use App\Models\Conference;
use App\Models\Enums\ConferenceStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Squire\Models\Country;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Conference>
 */
class ConferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Conference::class;

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
            'name' => $name . ' ' . $city . ' ' . $year,
            'path' => Str::slug($city),
            'status' => fake()->boolean(80) ? ConferenceStatus::Upcoming : ConferenceStatus::Archived,
            'date_start' => fake()->dateTimeBetween('-1 year', '+1 year'),
            'date_end' => fake()->dateTimeBetween('+1 year', '+2 year'),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Conference $conference) {
            $conference->setManyMeta([
                'publisher_name' => fake()->company(),
                'affiliation' => fake()->company(),
                'country' => Country::inRandomOrder()->first()->id,
                'location' => fake()->city(),
                'description' => fake()->paragraphs(3, true),
                'about' => fake()->paragraphs(4, true),
                'page_footer' => view('frontend.examples.footer')->render(),
            ]);
        });
    }
}
