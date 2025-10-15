<?php

namespace Database\Seeders;

use App\Models\BillType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'شراء مواد',
            'فواتير كهرباء',
            'فواتير مياه',
            'فواتير غاز',
            'مصاريف مكتبية',
        ];

        foreach ($types as $type) {
            BillType::create(['name' => $type]);
        }
    }
}
