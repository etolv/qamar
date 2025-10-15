<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrderPackage extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "order_package";
    public $incrementing = true;

    public function packageItems()
    {
        return $this->hasMany(OrderPackageItem::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
