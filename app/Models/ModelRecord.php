<?php

namespace App\Models;

use App\Enums\ModelLogEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ModelRecord extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'type' => ModelLogEnum::class
    ];

    public function model()
    {
        return $this->morphTo('model');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOnlyOrders(Builder $query)
    {
        $query->hasMorph('model', [Order::class]);
    }


    public function scopeOnlyBookings(Builder $query)
    {
        $query->hasMorph('model', [Booking::class]);
    }

    public function scopeCreated(Builder $query)
    {
        $query->where('type', ModelLogEnum::CREATE);
    }
}
