<?php

namespace App\Services;

use App\Enums\AttendanceStatusEnum;
use App\Enums\DepartmentEnum;
use App\Enums\ModelLogEnum;
use App\Enums\OverTimeStatusEnum;
use App\Enums\ProfitTypeEnum;
use App\Enums\ServiceStatusEnum;
use App\Enums\StatusEnum;
use App\Enums\VacationStatusEnum;
use App\Enums\VacationTypeEnum;
use App\Jobs\StoreTransactionJob;
use App\Models\Booking;
use App\Models\BookingProduct;
use App\Models\BookingService;
use App\Models\CafeteriaOrder;
use App\Models\CafeteriaOrderService;
use App\Models\CafeteriaOrderStock;
use App\Models\Employee;
use App\Models\GeneratedSalary;
use App\Models\ModelRecord;
use App\Models\Order;
use App\Models\OrderService;
use App\Models\OrderStock;
use App\Models\ProfitType;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Termwind\Components\Raw;

/**
 * Class GeneratedSalaryService.
 */
class GeneratedSalaryService extends BaseService
{

    public function __construct(protected EmployeeService $employeeService, protected SettingService $settingService) {}

    public function all($data = [], $withes = [], $paginated = false, $withTrashed = false)
    {
        $query = GeneratedSalary::when(isset($data['from']), function ($query) use ($data) {
            $query->where('created_at', '>=', $data['from']);
        })->when(isset($data['to']), function ($query) use ($data) {
            $query->where('created_at', '<=', $data['to']);
        })->when(isset($data['employee_id']), function ($query) use ($data) {
            $query->where('employee_id', $data['employee_id']);
        })->with($withes);
        return $paginated ? $query->paginate() : $query->get();
    }

    public function generate(array $data): array
    {
        $generate_salaries = [];
        foreach ($data['employees'] as $employee_id) {
            $salary_data = $this->generateSalary($employee_id, $data);
            $salary = GeneratedSalary::updateOrCreate(
                ['employee_id' => $employee_id, 'month' => $data['month']],
                $salary_data
            );
            $generate_salaries[] = $salary;
            StoreTransactionJob::dispatch($salary);
        }
        return $generate_salaries;
    }

    public function generateSalary(int $employee_id, array $data): array
    {
        $salary_data = array();
        $employee = $this->employeeService->show($employee_id);
        $salary = $employee->activeSalary;
        $previous_month = $data['month'] == 1 ? 12 : $data['month'] - 1;
        $salary_data['month'] = $data['month'];
        $salary_data['employee_id'] = $employee_id;
        $salary_data['salary_id'] = $salary->id;
        $salary_data['base_salary'] = (float)$salary->amount;
        $hourly_rate = $salary->amount / $salary->working_hours;
        $attendances = $employee->attendances()
            ->whereBetween('date', [$data['start'], $data['end']])->get();
        $salary_data['extra_hours'] = $attendances->where('extra_hours', '>', 0)
            ->where('overtime_status', OverTimeStatusEnum::OVERTIME)->sum('extra_hours') * 1.5;
        $salary_data['rounded_hours'] = $attendances->where('extra_hours', '>', 0)
            ->where('overtime_status', OverTimeStatusEnum::COMPENSATION)->sum('extra_hours');
        $salary_data['rounded_hours'] += GeneratedSalary::where('employee_id', $employee_id)
            ->where('month', $previous_month)->first()?->rounded_hours ?? 0;
        $salary_data['working_hours'] = $attendances->sum('total');
        $salary_data['missing_hours'] = $attendances->where('missing_hours', '>', 0)->count('missing_hours');
        if ($attendances->count()) {
            $shift = $attendances[0]?->shift;
        }
        $allWorkingDays = new Collection();
        // for ($date = Carbon::parse($data['start']); $date->lte(Carbon::parse($data['end'])); $date->addDay()) {
        //     continue;
        //     $exist_vacation = $employee->vacations()->where('start_date', '<=', $date)
        //         ->where('end_date', '>=', $date)
        //         ->where('status', VacationStatusEnum::APPROVED->value)
        //         ->where('type', '!=', VacationTypeEnum::UNPAID->value)
        //         ->exists();
        //     if ($date->dayOfWeek != $employee->holiday->value && !$exist_vacation) {
        //         if (!$attendances->where('date', $date->format('Y-m-d'))->first()) {
        //             $salary_data['missing_hours'] += $shift?->daily_hours ?? 0;
        //         }
        //         $allWorkingDays->push($date->format('Y-m-d'));
        //     }
        // }
        if ($salary_data['missing_hours'] > 0 && ($salary_data['extra_hours'] > 0 || $salary_data['rounded_hours'] > 0)) {
            if ($salary_data['rounded_hours'] >= $salary_data['missing_hours']) {
                $salary_data['rounded_hours'] -= $salary_data['missing_hours'];
                $salary_data['missing_hours'] = 0;
            } else {
                $salary_data['missing_hours'] -= $salary_data['rounded_hours'];
                $salary_data['rounded_hours'] = 0;
            }
            if ($salary_data['missing_hours'] > 0) {
                if ($salary_data['extra_hours'] >= $salary_data['missing_hours']) {
                    $salary_data['extra_hours'] -= $salary_data['missing_hours'];
                    $salary_data['missing_hours'] = 0;
                } else {
                    $salary_data['missing_hours'] -= $salary_data['extra_hours'];
                    $salary_data['extra_hours'] = 0;
                }
            }
        }
        // profit
        $salary_data['profit_total'] = 0;
        $salary_data['target_total'] = $this->calculateProfit($employee, $data);
        $salary_data['profit_percentage'] = (float)$salary->profit_percentage ?? $this->settingService->valueFromKey('profit_percentage');
        $salary_data['target'] = (float)$salary->target ?? $this->settingService->valueFromKey('employee_target');
        $total_employee_target = GeneratedSalary::whereEmployeeId($employee_id)->sum('target_total') ?? 0;
        $is_target_monthly = $this->settingService->valueFromKey('is_target_monthly');
        if (!$is_target_monthly) {
            $total_employee_target = 0;
        }
        if (($salary_data['target_total'] + $total_employee_target) >= $salary_data['target']) {
            $salary_data['profit_total'] = ($salary_data['target_total'] * $salary_data['profit_percentage']) / 100;
        }
        // gifts, advance, deduct
        $salary_data['gift'] = $employee->cashFlows()->gifts()
            ->whereBetween('due_date', [$data['start'], $data['end']])->sum('amount');
        $salary_data['advance'] = $employee->cashFlows()->advances()
            ->whereBetween('due_date', [$data['start'], $data['end']])->sum('amount');
        $salary_data['deduction'] = $employee->cashFlows()->deducts()
            ->whereBetween('due_date', [$data['start'], $data['end']])->sum('amount');
        // calculate overtime, total_deduction, total_extra, total
        $salary_data['overtime'] = $salary_data['extra_hours'] * $hourly_rate;
        $salary_data['total_deduction'] = $salary_data['deduction'] + $salary_data['advance'];
        if ($salary_data['missing_hours'])
            $salary_data['total_deduction'] += $salary_data['missing_hours'] * $hourly_rate;
        $salary_data['total_extra'] = $salary_data['overtime'] + $salary_data['gift'] + $salary_data['profit_total'];
        $salary_data['total'] = $salary_data['base_salary'] + $salary_data['total_extra'] - $salary_data['total_deduction'];
        return $salary_data;
    }

    public function calculateProfit(Employee $employee, array $data)
    {
        $total = 0;
        $profit_type = $employee->activeSalary->profit_type;
        switch ($profit_type) {
            case ProfitTypeEnum::PRODUCT:
                $total += $this->orderProductProfit($data, DepartmentEnum::SALON, $employee);
                $total += $this->orderProductProfit($data, DepartmentEnum::CAFETERIA, $employee);
                $total += $this->bookingProductProfit($data, $employee);
                break;
            case ProfitTypeEnum::SERVICE:
                $total += $this->orderServiceProfit($data, DepartmentEnum::SALON, $employee);
                $total += $this->orderServiceProfit($data, DepartmentEnum::CAFETERIA, $employee);
                $total += $this->bookingServiceProfit($data, $employee);
                break;
            case ProfitTypeEnum::SALON_ORDER:
                $total += $this->orderServiceProfit($data, DepartmentEnum::SALON);
                $total += $this->orderProductProfit($data, DepartmentEnum::SALON);
                $total += $this->bookingProductProfit($data);
                $total += $this->bookingServiceProfit($data);
                break;
            case ProfitTypeEnum::CAFETERIA_ORDER:
                $total += $this->orderServiceProfit($data, DepartmentEnum::CAFETERIA);
                $total += $this->orderProductProfit($data, DepartmentEnum::CAFETERIA);
                break;
            case ProfitTypeEnum::ALL_ORDER:
                $total += $this->orderProductProfit($data, DepartmentEnum::SALON);
                $total += $this->orderServiceProfit($data, DepartmentEnum::SALON);
                $total += $this->orderProductProfit($data, DepartmentEnum::CAFETERIA);
                $total += $this->orderServiceProfit($data, DepartmentEnum::CAFETERIA);
                $total += $this->bookingProductProfit($data);
                $total += $this->bookingServiceProfit($data);
                break;
            default:
                break;
        }
        return $total;
    }

    public function orderServiceProfit($data, DepartmentEnum $department, $employee = null)
    {
        if ($department == DepartmentEnum::CAFETERIA) {
            return CafeteriaOrderService::whereHas('cafeteriaOrder', function ($query) use ($data, $employee) {
                $query->whereBetween('created_at', [$data['start'], $data['end']])
                    ->where('status', StatusEnum::COMPLETED->value)->when($employee, function ($query) use ($employee) {
                        $query->whereRelation('modelRecord', 'user_id', $employee->user_id);
                    });
            })->sum(DB::raw('quantity * price')) ?? 0;
        } else {
            return OrderService::when($employee, function ($query) use ($employee) {
                $query->where('employee_id', $employee->id);
            })->where('status', ServiceStatusEnum::COMPLETED->value)
                ->whereBetween('created_at', [$data['start'], $data['end']])
                ->sum(DB::raw('price * quantity')) ?? 0;
        }
    }

    public function orderProductProfit($data, DepartmentEnum $department, $employee = null)
    {
        if ($department == DepartmentEnum::CAFETERIA) {
            return CafeteriaOrderStock::whereHas('cafeteriaOrder', function ($query) use ($data, $employee) {
                $query->whereBetween('created_at', [$data['start'], $data['end']])
                    ->where('status', StatusEnum::COMPLETED->value)->when($employee, function ($query) use ($employee) {
                        $query->whereRelation('modelRecord', 'user_id', $employee->user_id);
                    });
            })->sum(DB::raw('quantity * price')) ?? 0;
        } else {
            return OrderStock::whereHas('order', function ($query) use ($data, $employee) {
                $query->whereBetween('created_at', [$data['start'], $data['end']])
                    ->where('status', StatusEnum::COMPLETED->value)->when($employee, function ($query) use ($employee) {
                        $query->whereRelation('modelRecord', 'user_id', $employee->user_id);
                    });
            })->sum(DB::raw('quantity * price')) ?? 0;
        }
    }

    public function bookingServiceProfit($data, $employee = null)
    {
        return $booking_services = BookingService::when($employee, function ($query) use ($employee) {
            $query->where('employee_id', $employee->id);
        })->where('status', ServiceStatusEnum::COMPLETED->value)
            ->whereBetween('created_at', [$data['start'], $data['end']])
            ->sum(DB::raw('price * quantity')) ?? 0;
    }

    public function bookingProductProfit($data, $employee = null)
    {
        return BookingProduct::whereHas('booking', function ($query) use ($data, $employee) {
            $query->whereBetween('created_at', [$data['start'], $data['end']])
                ->where('status', StatusEnum::COMPLETED->value)->when($employee, function ($query) use ($employee) {
                    $query->whereRelation('modelRecord', 'user_id', $employee->user_id);
                });
        })->sum(DB::raw('quantity * price')) ?? 0;
    }
}
