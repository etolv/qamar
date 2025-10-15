<?php

namespace App\Models;

use App\Enums\VacationStatusEnum;
use App\Enums\VacationTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class Vacation extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'type' => VacationTypeEnum::class,
        'status' => VacationStatusEnum::class,
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
