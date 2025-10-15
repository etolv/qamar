<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Card extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function cardable()
    {
        return $this->morphTo('cardable');
    }

    public function scopeOnlyBranches(Builder $query)
    {
        $query->hasMorph('cardable', [Branch::class]);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
