<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookingService extends Pivot
{
    protected $primaryKey = 'id';
    protected $table = "booking_service";
    public $incrementing = true;

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }
}
