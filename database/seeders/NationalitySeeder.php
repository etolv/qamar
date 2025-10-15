<?php

namespace Database\Seeders;

use App\Models\Nationality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NationalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nationalities = [
            [
                'name' => 'سعودي',
                'country' => 'المملكة العربية السعودية'
            ],
            [
                'name' => 'سوري',
                'country' => "الجمهورية العربية السورية"
            ],
            [
                'name' => 'هندي',
                'country' => "الهند"
            ],
            [
                'name' => 'افغاني',
                'country' => "افغانستان"
            ],
            [
                'name' => 'فيلبيني',
                'country' => "الفيليبين"
            ],
        ];

        foreach ($nationalities as $nationality) {
            Nationality::firstOrCreate($nationality);
        }
    }
}
