<?php

namespace Database\Seeders;

use App\Enums\SettingTypeEnum;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'privacy_policy' => [
                'value' => '',
                'type' => SettingTypeEnum::TEXT->value
            ],
            'terms_and_conditions' => [
                'value' => '',
                'type' => SettingTypeEnum::TEXT->value
            ],
            'about_us' => [
                'value' => '',
                'type' => SettingTypeEnum::TEXT->value
            ],
            // 'employee_minimum_profit' => [
            //     'value' => '5000',
            //     'type' => SettingTypeEnum::NUMERIC->value
            // ],
            // 'employee_profit_percentage' => [
            //     'value' => '12',
            //     'type' => SettingTypeEnum::NUMERIC->value
            // ],
            // 'employee_target' => [
            //     'value' => '1000',
            //     'type' => SettingTypeEnum::NUMERIC->value
            // ],
            'instagram' => [
                'value' => 'https://www.instagram.com/qamarsamaya?r=nametag',
                'type' => SettingTypeEnum::STRING->value
            ],
            'snapchat' => [
                'value' => 'https://www.snapchat.com/add/athary_makeup',
                'type' => SettingTypeEnum::STRING->value
            ],
            'tiktok' => [
                'value' => 'https://www.tiktok.com/@athari_qamar_samaya?_t=8WnvXDZd0C2&_r=1',
                'type' => SettingTypeEnum::STRING->value
            ],
            'youtube' => [
                'value' => 'https://www.youtube.com/@qamarsamaya',
                'type' => SettingTypeEnum::STRING->value
            ],
            'email' => [
                'value' => 'qamarsamaya@gmail.com',
                'type' => SettingTypeEnum::STRING->value
            ],
            'phone' => [
                'value' => '966551114415',
                'type' => SettingTypeEnum::STRING->value
            ],
            'address' => [
                'value' => 'Riyadh, Saudi Arabia, Salman Al Farsi',
                'type' => SettingTypeEnum::STRING->value
            ],
            'cash_to_points' => [
                'value' => 5,
                'type' => SettingTypeEnum::NUMERIC->value,
                'description' => "Each 1 SAR will get x point"
            ],
            'points_to_cash' => [
                'value' => 5,
                'type' => SettingTypeEnum::NUMERIC->value,
                'description' => "Each 1 Point will get x SAR"
            ],
            'profit_percentage' => [
                'value' => '12',
                'type' => SettingTypeEnum::NUMERIC->value
            ],
            'is_target_monthly' => [
                'value' => false,
                'type' => SettingTypeEnum::BOOLEAN->value
            ],
            'tax' => [
                'value' => '15',
                'type' => SettingTypeEnum::NUMERIC->value,
                'description' => "Percentage"
            ],
            'loyalty_points_period' => [
                'value' => 1,
                'type' => SettingTypeEnum::NUMERIC->value,
                'description' => "Loyalty points period in months"
            ],
            'employee_expiration_reminder' => [
                'value' => 30,
                'type' => SettingTypeEnum::NUMERIC->value,
                'description' => "Employee expiration reminder in days"
            ],
        ];
        foreach ($settings as $key => $setting) {
            $setting['key'] = $key;
            Setting::firstOrCreate(['key' => $key], $setting);
        }
    }
}
