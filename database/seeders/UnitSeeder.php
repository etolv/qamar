<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            [
                'name' => 'كرتون',
                'unit_id' => null,
                'factor' => 1
            ],
            [
                'name' => 'عبوة',
                'unit_id' => 1,
                'factor' => 10
            ],
            [
                'name' => 'شدة',
                'unit_id' => null,
                'factor' => 1
            ],
            [
                'name' => 'حبة',
                'unit_id' => 3,
                'factor' => 12
            ],
            [
                'name' => 'تيوب',
                'unit_id' => null,
                'factor' => 1
            ],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate($unit);
        }
    }
}
