<?php

namespace App\Models;

use App\Enums\SupplierTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Supplier extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'type' => SupplierTypeEnum::class
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function cards()
    {
        return $this->morphMany(Card::class, 'cardable');
    }

    public function account()
    {
        return $this->morphOne(Account::class, 'model');
    }
}
