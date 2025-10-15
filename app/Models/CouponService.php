<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CouponService extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "coupon_service";
    public $incrementing = true;

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'id');
    }
}
