<?php

namespace App\Models;

use App\Enums\CashFlowStatusEnum;
use App\Enums\CashFlowTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CashFlow extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $casts = [
        'status' => CashFlowStatusEnum::class,
        'type' => CashFlowTypeEnum::class
    ];

    public function flowable()
    {
        return $this->morphTo('flowable');
    }

    public function scopeDeducts(Builder $query)
    {
        $query->where('type', CashFlowTypeEnum::DEDUCT->value);
    }

    public function scopeAdvances(Builder $query)
    {
        $query->where('type', CashFlowTypeEnum::ADVANCE->value);
    }

    public function scopeGifts(Builder $query)
    {
        $query->where('type', CashFlowTypeEnum::GIFT->value);
    }

    public function scopeExpenses(Builder $query)
    {
        $query->where('type', CashFlowTypeEnum::EXPENSE->value);
    }
}
