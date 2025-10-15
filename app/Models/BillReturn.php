<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillReturn extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function billReturnStocks()
    {
        return $this->hasMany(BillReturnStock::class);
    }

    public function payments()
    {
        return $this->morphMany(Payment::class, 'model');
    }
}
