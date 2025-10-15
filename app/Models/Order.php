<?php

namespace App\Models;

use App\Enums\DepartmentEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $casts = [
        'status' => StatusEnum::class,
        'payment_status' => PaymentStatusEnum::class,
        'payment_type' => PaymentTypeEnum::class,
        'department' => DepartmentEnum::class
    ];

    public function rates()
    {
        return $this->morphMany(Rate::class, 'model');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'model');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function gifter()
    {
        return $this->belongsTo(Customer::class, 'gifter_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function stocks()
    {
        return $this->belongsToMany(Stock::class, 'order_stock');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'order_service');
    }

    public function packages()
    {
        return $this->belongsToMany(Package::class, 'order_package');
    }

    public function orderStocks()
    {
        return $this->hasMany(OrderStock::class);
    }

    public function orderServices()
    {
        return $this->hasMany(OrderService::class);
    }

    public function orderPackages()
    {
        return $this->hasMany(OrderPackage::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'model');
    }

    public function modelRecord()
    {
        return $this->morphOne(ModelRecord::class, 'model');
    }

    /**
     * ✅ Subtotal (غير شامل الضريبة)
     * إذا كانت الأسعار المخزنة شاملة الضريبة → نقسم على 1.15
     */
    public function getSubtotalFixedAttribute()
    {
        $sumWithVat = $this->orderServices->sum('price');
        return round($sumWithVat / 1.15, 2);
    }

    /**
     * ✅ Tax (قيمة الضريبة فقط)
     */
    public function getTaxFixedAttribute()
    {
        return round($this->total_fixed - $this->subtotal_fixed, 2);
    }

    /**
     * ✅ Total (شامل الضريبة)
     */
    public function getTotalFixedAttribute()
    {
        return round($this->orderServices->sum('price'), 2);
    }
}
