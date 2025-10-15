<?php

namespace App\Models;

use App\Enums\TripStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'status' => TripStatusEnum::class
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function tripable()
    {
        return $this->morphTo('tripable');
    }
}
