<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Brand>
 */
class BrandFactory extends Factory
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
            'ar' => [
                'name' => $faker->unique()->name()
            ],
            'en' => [
                'name' => fake()->unique()->name()
            ],
            'slug' => fake()->unique()->word()
        ];
    }
}
