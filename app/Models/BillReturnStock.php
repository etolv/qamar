<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Svg\Gradient\Stop;

class BillReturnStock extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "bill_return_stock";
    public $incrementing = true;

    public function billReturn()
    {
        return $this->belongsTo(BillReturn::class, 'bill_return_id', 'id');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'stock_id', 'id');
    }

    public function product()
    {
        return $this->hasOneThrough(Product::class, Stock::class, 'id', 'id', 'stock_id', 'product_id');
    }

    public function transfer()
    {
        return $this->morphOne(Transfer::class, 'to');
    }
}
