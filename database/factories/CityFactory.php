<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\City>
 */
class CityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('ar_SA');
        return [
            'state_id' => State::factory()->create()->id,
            'ar' => [
                'name' => fake()->unique()->city()
            ],
            'en' => [
                'name' => fake()->unique()->city()
            ],
        ];
    }
}
