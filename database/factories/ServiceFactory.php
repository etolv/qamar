<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'sku' => $this->faker->ean8,
            'description' => $this->faker->sentence(10),
            'category_id' => Category::factory()->create()->id,
            'price' => $this->faker->numberBetween(10, 1000),
            'department' => $this->faker->numberBetween(1, 2),
        ];
    }
}
