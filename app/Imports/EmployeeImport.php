<?php

namespace App\Imports;

use App\Enums\SectionEnum;
use App\Models\Branch;
use App\Models\City;
use App\Models\Employee;
use App\Models\Job;
use App\Models\Nationality;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;

class EmployeeImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        DB::beginTransaction();
        $data_row = null;
        try {
            $data = $collection->skip(1); // skip header
            $data->map(function ($row) use (&$data_row) {
                $data_row = $row;
                if (!$row[0] || !$row[2]) return;
                $dial_code = substr($row[2], 0, 3);
                $phone = substr($row[2], 3);
                $user = User::firstOrCreate(['name' => $row[0]], [
                    'dial_code' => $dial_code,
                    'phone' => $phone,
                    'email' => $row[3],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password')
                ]);
                // get category or create new one
                $nationality = Nationality::firstOrCreate(['name' => $row[1]], [
                    'country' => $row[1]
                ]);
                $job = Job::firstOrCreate(['title' => $row[5]], [
                    'section' => SectionEnum::STAFF->value
                ]);
                $employee = Employee::firstOrCreate(['user_id' => $user->id], [
                    'city_id' => City::first()->id,
                    'nationality_id' => $nationality->id,
                    'branch_id' => Branch::first()->id,
                    'job_id' => $job->id,
                    // 'birthday' => Carbon::parse(str_replace(' ', '', $row[4]))->format('Y-m-d'),
                    'residence_number' => $row[8],
                    // 'residence_expiration' => Carbon::parse(str_replace(' ', '', $row[9]))->format('Y-m-d')
                ]);
                $user->account()->associate($employee);
                $user->save();
            });
        } catch (\Exception $e) {
            dd($data_row, $e);
        }
        DB::commit();
    }
}
