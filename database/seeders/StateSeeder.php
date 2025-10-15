<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Municipal;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $state = State::create([
            'ar' => [
                'name' => 'الرياض'
            ],
            'en' => [
                'name' => 'Riyadh'
            ]
        ]);
        $city = City::create([
            'ar' => [
                'name' => 'الرياض'
            ],
            'en' => [
                'name' => 'Riyadh'
            ],
            'state_id' => $state->id
        ]);
        City::factory(20)->create();

        Municipal::create([
            'ar' => [
                'name' => 'الرياض'
            ],
            'en' => [
                'name' => 'Riyadh'
            ],
            'city_id' => $city->id
        ]);
    }
}
