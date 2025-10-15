<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate([
            'phone' => '54700800',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
        $admin = new Admin();
        $admin->user()->associate($user);
        $admin->save();
        $user->account()->associate($admin);
        $user->assignRole('super_admin');
        $user->save();

        $user = User::firstOrCreate([
            'phone' => '0992990128',
        ], [
            'name' => 'Softlix Admin',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);
        $admin = new Admin();
        $admin->user()->associate($user);
        $admin->save();
        $user->account()->associate($admin);
        $user->assignRole('super_admin');
        $user->save();
    }
}
