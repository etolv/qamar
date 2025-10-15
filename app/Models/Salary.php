<?php

namespace App\Models;

use App\Enums\ProfitTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'profit_type' => ProfitTypeEnum::class
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
