<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeShift extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "employee_shift";
    public $incrementing = true;

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
    }

    public function attendances()
    {
        return $this->hasManyThrough(
            Attendance::class,
            Shift::class,
            'id', // Foreign key on the Shift table
            'shift_id', // Foreign key on the Attendance table
            'shift_id', // Local key on the EmployeeShift table
            'id'        // Local key on the Shift table
        );
    }
}
