<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderServiceReturn extends Model
{
    use HasFactory;
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function orderService()
    {
        return $this->belongsTo(OrderService::class, 'order_service_id', 'id');
    }

    public function order_service()
    {
        return $this->belongsTo(OrderService::class, 'order_service_id', 'id');
    }
}
