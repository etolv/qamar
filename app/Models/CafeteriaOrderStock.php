<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Casts\Attribute;

class CafeteriaOrderStock extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "cafeteria_order_stock";
    public $incrementing = true;

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

    public function cafeteriaOrder()
    {
        return $this->belongsTo(CafeteriaOrder::class, 'cafeteria_order_id', 'id');
    }
}
