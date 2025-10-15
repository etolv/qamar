<?php

namespace App\Models;

use App\Enums\DepartmentEnum;
use App\Enums\StockWithdrawalTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockWithdrawal extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'type' => StockWithdrawalTypeEnum::class,
        'department' => DepartmentEnum::class
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }

    public function product()
    {
        return $this->hasOneThrough(Product::class, Stock::class, 'id', 'id', 'stock_id', 'product_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
