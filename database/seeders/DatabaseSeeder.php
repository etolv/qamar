<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AccountSeeder::class,
            StateSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            JobSeeder::class,
            UnitSeeder::class,
            NationalitySeeder::class,
            // BrandSeeder::class,
            // CategorySeeder::class,
            BranchSeeder::class,
            // SupplySeeder::class,
            // ProductSeeder::class,
            // ServiceSeeder::class,
            // EmployeeSeeder::class,
            CustomerSeeder::class,
            SettingSeeder::class,
            RateReasonSeeder::class,
            BillTypeSeeder::class,
        ]);
    }
}
