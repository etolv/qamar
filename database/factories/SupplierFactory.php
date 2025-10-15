<?php

namespace Database\Factories;

use App\Enums\SupplierTypeEnum;
use App\Models\City;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'company' => $this->faker->company(),
            'tax_number' => $this->faker->phoneNumber(),
            'bank_number' => $this->faker->phoneNumber(),
            'email' => $this->faker->email(),
            'city_id' => City::all()->random()->id,
            'type' => $this->faker->numberBetween(SupplierTypeEnum::ONLINE->value, SupplierTypeEnum::CAFETERIA->value),
        ];
    }
}
