<?php

namespace Database\Seeders;

use App\Models\Rate;
use App\Models\RateReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RateReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rates = [
            'السعر غير مطابق للوصف',
            'الخدمة غير مطابقة للوصف',
            'اخرى',
        ];
        foreach ($rates as $rate) {
            RateReason::create(['name' => $rate]);
        }
    }
}
