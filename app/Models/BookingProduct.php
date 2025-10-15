<?php

namespace App\Models;

use App\Enums\ItemTypeEnum;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BookingProduct extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "booking_product";
    public $incrementing = true;

    protected $casts = [
        'type' => ItemTypeEnum::class
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class, 'product_id', 'id');
    }

    public function product()
    {
        return $this->hasOneThrough(Product::class, Stock::class, 'id', 'id', 'product_id', 'id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    public function bookingService()
    {
        return $this->belongsTo(BookingService::class, 'booking_service_id', 'id');
    }
}
