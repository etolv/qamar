<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BillProduct extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "bill_product";
    public $incrementing = true;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id', 'id');
    }

    public function unit_purchase()
    {
        return $this->belongsTo(Unit::class, 'purchase_unit_id');
    }

    public function unit_retail()
    {
        return $this->belongsTo(Unit::class, 'retail_unit_id');
    }

    public function transfer()
    {
        return $this->morphOne(Transfer::class, 'from');
    }
}
