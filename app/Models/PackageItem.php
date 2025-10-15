<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageItem extends Model
{
    use HasFactory;
    // use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function getTypeAttribute()
    {
        return class_basename($this->item_type);
    }

    public function item()
    {
        return $this->morphTo('item');
    }
}
