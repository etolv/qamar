<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryService extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "category_service";
    public $incrementing = true;

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
