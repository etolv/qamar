<?php

namespace App\Models;

use App\Enums\OrderableTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CafeteriaOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id', 'crated_at', 'updated_at'];

    protected $casts = [
        'type' => OrderableTypeEnum::class,
        'status' => StatusEnum::class,
        'payment_status' => PaymentStatusEnum::class
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'cafeteria_order_stock');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'cafeteria_order_service');
    }
    public function cafeteriaOrderStocks()
    {
        return $this->hasMany(CafeteriaOrderStock::class);
    }

    public function cafeteriaOrderServices()
    {
        return $this->hasMany(CafeteriaOrderService::class);
    }

    public function orderable(): MorphTo
    {
        return $this->morphTo('orderable');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'model');
    }

    public function modelRecord()
    {
        return $this->morphOne(ModelRecord::class, 'model');
    }
}
