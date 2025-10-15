<?php

namespace App\Models;

use App\Enums\ItemTypeEnum;
use App\Enums\ServiceStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Casts\Attribute;

class OrderService extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "order_service";
    public $incrementing = true;
    protected $casts = [
        'status' => ServiceStatusEnum::class,
        'type' => ItemTypeEnum::class
    ];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            fn(string|null $value) => Carbon::parse($value)->format('Y-m-d H:i'),
        );
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function sessions()
    {
        return $this->hasMany(OrderServiceSession::class, 'order_service_id', 'id');
    }

    public function return()
    {
        return $this->hasOne(OrderServiceReturn::class, 'order_service_id', 'id');
    }

    public function postpone()
    {
        return $this->hasOne(OrderServicePostPone::class, 'order_service_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
