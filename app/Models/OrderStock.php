<?php

namespace App\Models;

use App\Enums\ItemTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Casts\Attribute;

class OrderStock extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "order_stock";
    public $incrementing = true;
    protected $casts = [
        'type' => ItemTypeEnum::class
    ];

    protected function createdAt(): Attribute
    {
        return Attribute::get(
            fn(string|null $value) => Carbon::parse($value)->format('Y-m-d H:i'),
        );
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }

    public function product()
    {
        return $this->hasOneThrough(Product::class, Stock::class, 'id', 'id', 'stock_id', 'product_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function orderService()
    {
        return $this->belongsTo(OrderService::class);
    }
}
