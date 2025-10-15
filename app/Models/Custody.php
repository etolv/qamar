<?php

namespace App\Models;

use App\Enums\CustodyStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Custody extends Model
{
    use HasFactory;

    protected $guarded  = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'status' => CustodyStatusEnum::class
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
