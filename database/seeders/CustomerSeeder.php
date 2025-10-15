<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Customer',
            'password' => Hash::make('password'),
            'phone' => '0993998608',
            'email' => 'mohammad.khaddam.714@gmail.com',
            'email_verified_at' => now()
        ]);
        $customer = new Customer();
        $customer->city_id = City::first()->id;
        $customer->user()->associate($user);
        $customer->save();
        $user->account()->associate($customer);
        $user->save();
    }
}
