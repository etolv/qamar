<?php

namespace App\Models;

use App\Enums\TaskStatusEnum;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeTask extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "employee_task";
    public $incrementing = true;

    protected $casts = [
        'status' => TaskStatusEnum::class
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function user()
    {
        return $this->hasOneThrough(User::class, Employee::class, 'id', 'id', 'employee_id', 'user_id');
    }
}
