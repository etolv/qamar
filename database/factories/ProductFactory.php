<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => $this->faker->ean8(),
            'name' => $this->faker->word(),
            'category_id' => Category::factory()->create()->id,
            'brand_id' => Brand::factory()->create()->id,
            'min_quantity' => $this->faker->numberBetween(1, 10),
            'consumption_type' => $this->faker->numberBetween(1, 3),
            'refundable' => $this->faker->boolean(),
            'department' => $this->faker->numberBetween(1, 2),
        ];
    }
}
