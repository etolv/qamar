<?php

namespace App\Models;

use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Payment extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;


    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'type' => PaymentTypeEnum::class,
        'status' => PaymentStatusEnum::class
    ];

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    public function card()
    {
        return $this->belongsTo(Card::class);
    }
}
