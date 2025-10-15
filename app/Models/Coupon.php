<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Psy\TabCompletion\Matcher\FunctionsMatcher;

class Coupon extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    // public function couponable()
    // {
    //     return $this->morphTo();
    // }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'coupon_service');
    }
    public function couponServices()
    {
        return $this->hasMany(CouponService::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'coupon_product');
    }

    public function couponProducts()
    {
        return $this->hasMany(CouponProduct::class);
    }
}
