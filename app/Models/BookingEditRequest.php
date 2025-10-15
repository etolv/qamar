<?php

namespace App\Models;

use App\Enums\RequestStatusEnum;
use App\Enums\RequestTypesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingEditRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'status' => RequestStatusEnum::class,
        'type' => RequestTypesEnum::class
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->hasOneThrough(Customer::class, Booking::class);
    }
}
