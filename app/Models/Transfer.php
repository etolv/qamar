<?php

namespace App\Models;

use App\Enums\TransferTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'type' => TransferTypeEnum::class
    ];

    public function from(): MorphTo
    {
        return $this->morphTo('from');
    }

    public function to(): MorphTo
    {
        return $this->morphTo('to');
    }
}
