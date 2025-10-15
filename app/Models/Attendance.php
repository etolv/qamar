<?php

namespace App\Models;

use App\Enums\AttendanceStatusEnum;
use App\Enums\OverTimeStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'overtime_status' => OverTimeStatusEnum::class,
        'status' => AttendanceStatusEnum::class,
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
