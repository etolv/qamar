<?php

namespace App\Models;

use App\Enums\SettingTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $casts = [
        'type' => SettingTypeEnum::class
    ];

    protected $guarded = ['id', 'created_at', 'updated_at'];
}
