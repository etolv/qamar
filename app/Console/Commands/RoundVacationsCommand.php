<?php

namespace App\Console\Commands;

use App\Enums\CashFlowStatusEnum;
use App\Enums\CashFlowTypeEnum;
use App\Enums\VacationStatusEnum;
use App\Enums\VacationTypeEnum;
use App\Models\Card;
use App\Models\CashFlow;
use App\Models\Employee;
use App\Models\Vacation;
use App\Services\CashFlowService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RoundVacationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:round-vacations-command';

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
        Employee::chunk(10, function ($employees) {
            foreach ($employees as $employee) {
                // rounded vacations
                $rounded_vacations = $employee->vacations()->where('type', VacationTypeEnum::ROUNDED->value)
                    ->where('status', VacationStatusEnum::PENDING->value);
                $employee->update([
                    'remaining_vacation_days' => DB::raw('remaining_vacation_days + vacation_days + ' . $rounded_vacations->sum('days') ?? 0),
                ]);
                $rounded_vacations->update(['status' => VacationStatusEnum::ROUNDED->value]);
                // cashed vacations
                $cashed_vacations = $employee->vacations()->where('type', VacationTypeEnum::CASHED->value)
                    ->where('status', VacationStatusEnum::PENDING->value);
                if ($cashed_vacations->count()) {
                    $salary = $employee->salaries()->whereBetween('start_date', [
                        Carbon::now()->subDay()->startOfYear()->format('Y-m-d'),
                        Carbon::now()->subDay()->endOfYear()->format('Y-m-d')
                    ])->first() ?? $employee->activeSalary;
                    if (!$salary) continue;
                    $hourly_rate = $salary->amount / $salary->working_hours;
                    $cash = ($cashed_vacations->sum('days') * 8) * $hourly_rate;
                    $cash = resolve(CashFlowService::class)->store([
                        'amount' => $cash,
                        'type' => CashFlowTypeEnum::GIFT->value,
                        'flowable_id' => $employee->id,
                        'flowable_type' => Employee::class,
                        'due_date' => Carbon::now()->addDay()->endOfMonth()->format('Y-m-d'),
                        'reason' => _t('Cash Rounded Vacations'),
                        'status' => CashFlowStatusEnum::PENDING->value,
                        'split_months_count' => 1
                    ]);
                    $cashed_vacations->update(['status' => VacationStatusEnum::CASHED->value]);
                }
            }
        });
    }
}
