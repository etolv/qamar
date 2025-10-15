<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CouponProduct extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "coupon_product";
    public $incrementing = true;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }
}
