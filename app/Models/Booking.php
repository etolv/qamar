<?php

namespace App\Models;

use App\Enums\PaymentStatusEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'status' => StatusEnum::class,
        'payment_status' => PaymentStatusEnum::class
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function addressModel()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    // public function driver()
    // {
    //     return $this->belongsTo(Employee::class);
    // }

    public function rates()
    {
        return $this->morphMany(Rate::class, 'model');
    }

    public function products()
    {
        return $this->belongsToMany(Stock::class, 'booking_product');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'booking_service');
    }

    public function bookingProducts()
    {
        return $this->hasMany(BookingProduct::class);
    }

    public function bookingServices()
    {
        return $this->hasMany(BookingService::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'model');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'model');
    }
  public function coupon()
  {
    return $this->belongsTo(Coupon::class, 'coupon_id');
  }

}
