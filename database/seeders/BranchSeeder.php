<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Brand;
use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Branch::factory(10)->create();
        Branch::create([
            'name' => 'Main Branch',
            'address' => 'Ryiad',
            'city_id' => City::factory()->create()->id
        ]);
    }
}
