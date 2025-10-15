<?php

namespace App\Models;

use App\Enums\ShiftTypeEnum;
use App\Enums\WeekDaysEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'holiday' => WeekDaysEnum::class,
        'type' => ShiftTypeEnum::class
    ];

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'employee_shift');
    }

    public function employeeShifts()
    {
        return $this->hasMany(EmployeeShift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
