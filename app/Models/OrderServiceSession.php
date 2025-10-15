<?php

namespace App\Models;

use App\Enums\SessionStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderServiceSession extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'status' => SessionStatusEnum::class
    ];

    public function orderService()
    {
        return $this->belongsTo(OrderService::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
