<?php

namespace App\Models;

use App\Enums\TaskStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Task extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employeeTasks()
    {
        return $this->hasMany(EmployeeTask::class);
    }
}
