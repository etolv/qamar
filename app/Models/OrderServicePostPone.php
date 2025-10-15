<?php

namespace App\Models;

use App\Enums\ServiceStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderServicePostPone extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'status' => ServiceStatusEnum::class
    ];

    public function orderService()
    {
        return $this->belongsTo(OrderService::class, 'order_service_id', 'id');
    }

    public function order_service()
    {
        return $this->belongsTo(OrderService::class, 'order_service_id', 'id');
    }
}
