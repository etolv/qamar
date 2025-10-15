<?php

namespace App\Models;

use App\Enums\MonthsEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneratedSalary extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'month' => MonthsEnum::class
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'model');
    }
}
