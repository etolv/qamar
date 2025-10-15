<?php

namespace Database\Factories;

use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->name(),
            'address' => fake()->unique()->address(),
            'street' => fake()->unique()->address(),
            'building' => fake()->unique()->address(),
            'is_physical' => rand(0, 1),
            'city_id' => City::factory()->create()->id
        ];
    }
}
