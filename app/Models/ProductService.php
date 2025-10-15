<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductService extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "product_service";
    public $incrementing = true;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }
}
