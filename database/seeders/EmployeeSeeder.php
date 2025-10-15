<?php

namespace Database\Seeders;

use App\Enums\SectionEnum;
use App\Models\Branch;
use App\Models\City;
use App\Models\Employee;
use App\Models\Job;
use App\Models\Nationality;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // foreach (Job::get() as $job) {
        // }
        $user = User::create([
            'name' => 'Salon Cashier',
            'password' => Hash::make('password'),
            'phone' => '999222333',
            'email_verified_at' => now()
        ]);
        $employee = new Employee();
        $employee->city_id = City::first()->id;
        $employee->branch_id = Branch::first()->id;
        $employee->nationality_id = Nationality::first()->id;
        $employee->job_id = Job::where('title', 'كاشير صالة')->first()->id;
        $employee->employee_no = 2;
        $employee->user()->associate($user);
        $employee->save();
        $user->account()->associate($employee);
        $user->assignRole('orders_cashier');
        $user->save();
        $employee->salaries()->create([
            'start_date' => Carbon::now()->format('Y-m-d'),
            'amount' => 3000
        ]);

        // hairdressing
        $user = User::create([
            'name' => 'Booking Hair stylist',
            'password' => Hash::make('password'),
            'phone' => '54700802',
            'email_verified_at' => now()
        ]);
        $employee = new Employee();
        $employee->city_id = City::first()->id;
        $employee->branch_id = Branch::first()->id;
        $employee->nationality_id = Nationality::first()->id;
        $employee->job_id = Job::where('title', 'مهنية منزلية')->first()->id;
        $employee->employee_no = 3;
        $employee->user()->associate($user);
        $employee->save();
        $user->account()->associate($employee);
        $user->assignRole('bookings_hairdresser');
        $user->save();
        $employee->salaries()->create([
            'start_date' => Carbon::now()->format('Y-m-d'),
            'amount' => 3000
        ]);

        // orders hairdresser
        $user = User::create([
            'name' => 'Salon Hair stylist',
            'password' => Hash::make('password'),
            'phone' => '54700804',
            'email_verified_at' => now()
        ]);
        $employee = new Employee();
        $employee->city_id = City::first()->id;
        $employee->branch_id = Branch::first()->id;
        $employee->nationality_id = Nationality::first()->id;
        $employee->job_id = Job::where('title', 'مهنية صالة')->first()->id;
        $employee->employee_no = 4;
        $employee->user()->associate($user);
        $employee->save();
        $user->account()->associate($employee);
        $user->assignRole('orders_hairdresser');
        $user->save();
        $employee->salaries()->create([
            'start_date' => Carbon::now()->format('Y-m-d'),
            'amount' => 3000
        ]);
    }
}
