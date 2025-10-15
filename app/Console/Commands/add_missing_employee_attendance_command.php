<?php

namespace App\Console\Commands;

use App\Enums\AttendanceStatusEnum;
use App\Enums\VacationStatusEnum;
use App\Enums\VacationTypeEnum;
use App\Models\Attendance;
use App\Models\EmployeeShift;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class add_missing_employee_attendance_command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add_missing_employee_attendance_command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        // DB::enableQueryLog();
        // logger('query log', [DB::getQueryLog()]);
        $missing_shifts = EmployeeShift::where('date', $today)->get();
        foreach ($missing_shifts as $employee_shift) {
            if (!Attendance::where('employee_id', $employee_shift->employee_id)->where('date', $today)->exists()) {
                $exists_vacation = $employee_shift->employee->vacations()->where('start_date', '<=', $today)
                    ->where('end_date', '>=', $today)
                    ->where('status', VacationStatusEnum::APPROVED->value)
                    ->where('type', '!=', VacationTypeEnum::UNPAID->value)
                    ->exists();
                $data = [
                    "employee_id" => $employee_shift->employee_id,
                    "date" => $today,
                    "start" => null,
                    "end" => null,
                ];
                $attendance = resolve(AttendanceService::class)->store($data);
            }
        }
    }
}
