<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function transfersFrom()
    {
        return $this->morphMany(Transfer::class, 'from');
    }

    public function transfersTo()
    {
        return $this->morphMany(Transfer::class, 'to');
    }

    public function billProduct()
    {
        return $this->hasOneThrough(
            BillProduct::class, // Final model to reach
            Transfer::class, // Intermediate model
            'to_id', // Foreign key on Transfer table pointing to Stock (intermediate → Stock)
            'id', // Foreign key on BillProduct table pointing to Transfer (final → intermediate)
            'id', // Local key on Stock table (Stock → Transfer)
            'from_id' // Local key on Transfer table pointing to BillProduct (Transfer → BillProduct)
        )->where('transfers.to_type', Stock::class)
            ->where('transfers.from_type', BillProduct::class);
    }
}
