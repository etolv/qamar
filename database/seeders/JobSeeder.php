<?php

namespace Database\Seeders;

use App\Enums\SectionEnum;
use App\Models\Job;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobs = [
            'branch_manager' => [
                'title' => 'مدير فرع',
                'section' => SectionEnum::MANAGEMENT->value
            ],
            'sales_specialist' => [
                'title' => 'مسؤول مبيعات',
                'section' => SectionEnum::SALES->value
            ],
            'procurement_specialist' => [
                'title' => 'مسؤول مشتريات',
                'section' => SectionEnum::PROCUREMENT->value
            ],
            'stock_specialist' => [
                'title' => 'مسؤول مخزون',
                'section' => SectionEnum::WAREHOUSE->value
            ],
            'branch_representative' => [
                'title' => 'مشرف صالة',
                'section' => SectionEnum::MANAGEMENT->value
            ],
            'hr_manager' => [
                'title' => 'مدير موارد بشرية',
                'section' => SectionEnum::MANAGEMENT->value
            ],
            'bookings_cashier' => [
                'title' => 'كاشير خدمات منزلية',
                'section' => SectionEnum::STAFF->value
            ],
            'orders_cashier' => [
                'title' => 'كاشير صالة',
                'section' => SectionEnum::STAFF->value
            ],
            'cafeteria_cashier' => [
                'title' => 'كاشير كافيتيريا',
                'section' => SectionEnum::STAFF->value
            ],
            'cafeteria_cashier' => [
                'title' => 'كاشير كافيتريا',
                'section' => SectionEnum::CAFETERIA->value
            ],
            'bookings_hairdresser' => [
                'title' => 'مهنية منزلية',
                'section' => SectionEnum::STAFF->value
            ],
            'orders_hairdresser' => [
                'title' => 'مهنية صالة',
                'section' => SectionEnum::STAFF->value
            ],
            'employee' => [
                'title' => 'موظف',
                'section' => SectionEnum::STAFF->value
            ],
            // 'representative_driver' => [
            //     'title' => 'سائق مندوب',
            //     'section' => SectionEnum::STAFF->value
            // ],
            'sanitation_worker' => [
                'title' => 'عامل نظافة',
                'section' => SectionEnum::STAFF->value
            ],
        ];

        foreach ($jobs as $index => $job) {
            $role = Role::firstOrCreate(['name' => $index]);
            $role->syncPermissions(['read_order', 'create_order', 'read_city', 'read_category', 'read_stock', 'read_product']);
            $job_model = Job::firstOrCreate($job);
        }
    }
}
