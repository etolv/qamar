<?php

namespace App\Models;

use App\Enums\RateTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Rate extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = ['type' => RateTypeEnum::class];

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    public function reason()
    {
        return $this->belongsTo(RateReason::class, 'rate_reason_id');
    }
}
