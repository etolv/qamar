<?php

namespace App\Models;

use App\Enums\BillTypeEnum;
use App\Enums\DepartmentEnum;
use Attribute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Bill extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'type' => BillTypeEnum::class,
        'department' => DepartmentEnum::class,
    ];

    public function billType()
    {
        return $this->belongsTo(BillType::class, 'bill_type_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function transfers()
    {
        return $this->morphMany(Transfer::class, 'from');
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'model');
    }

    public function billProducts()
    {
        return $this->hasMany(BillProduct::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'bill_product');
    }

    public function scopeSalon($query)
    {
        return $query->where('department', DepartmentEnum::SALON->value);
    }

    public function scopePurchases($query)
    {
        return $query->where('type', BillTypeEnum::PURCHASE->value);
    }

    public function scopeExpenses($query)
    {
        return $query->where('type', BillTypeEnum::EXPENSE->value);
    }

    // Scope for filtering cafeteria bills
    public function scopeCafeteria($query)
    {
        return $query->where('department', DepartmentEnum::CAFETERIA->value);
    }

    public function transactions()
    {
        return $this->morphMany(Transaction::class, 'model');
    }

    public function modelRecord()
    {
        return $this->morphOne(ModelRecord::class, 'model');
    }
}
