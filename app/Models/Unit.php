<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function parent()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function units()
    {
        return $this->hasMany(Unit::class, 'category_id');
    }
}
