<?php

namespace App\Models;

use App\Enums\ConsumptionTypeEnum;
use App\Enums\DepartmentEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'consumption_type' => ConsumptionTypeEnum::class,
        'department' => DepartmentEnum::class
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'product_service');
    }

    public function product_service()
    {
        return $this->hasMany(ProductService::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }
}
