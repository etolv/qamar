<?php

namespace App\Models;

use App\Enums\WeekDaysEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Employee extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'holiday' => WeekDaysEnum::class
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function orderServices()
    {
        return $this->hasMany(OrderService::class);
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function shifts()
    {
        return $this->belongsToMany(Shift::class, 'employee_shift');
    }

    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }

    public function vacations()
    {
        return $this->hasMany(Vacation::class);
    }

    public function cashFlows()
    {
        return $this->morphMany(CashFlow::class, 'flowable');
    }

    public function activeSalary()
    {
        return $this->hasOne(Salary::class)->whereNull('end_date')->latest();
    }

    public function scopeWithoutSalaries(Builder $query)
    {
        $query->whereDoesntHave('salaries')
            ->orWhereDoesntHave('salaries', function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', Carbon::now());
            });
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

    public function account()
    {
        return $this->morphOne(Account::class, 'model');
    }

    public function employeeInfos()
    {
        return $this->hasMany(EmployeeInfo::class);
    }
}
