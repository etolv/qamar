<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Service extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function parent()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_service');
    }

    public function product_service()
    {
        return $this->hasMany(ProductService::class);
    }

    public function productServices()
    {
        return $this->hasMany(ProductService::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_service');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_service');
    }
    public function couponServices()
    {
        return $this->hasMany(CouponService::class);
    }

    public function orderServices()
    {
        return $this->hasMany(OrderService::class);
    }

    public function cafeteriaOrderServices()
    {
        return $this->hasMany(CafeteriaOrderService::class);
    }
}
