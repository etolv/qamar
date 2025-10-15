<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Package extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function items()
    {
        return $this->hasMany(PackageItem::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_package');
    }

    public function orderPackages()
    {
        return $this->hasMany(OrderPackage::class);
    }

    public function stockItems()
    {
        return $this->hasMany(PackageItem::class)->where('item_type', Stock::class);
    }

    public function serviceItems()
    {
        return $this->hasMany(PackageItem::class)->where('item_type', Service::class);
    }
}
