<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function model()
    {
        return $this->morphTo('model');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function fromTransactions()
    {
        return $this->hasMany(Transaction::class, 'from_account_id', 'id');
    }

    public function toTransactions()
    {
        return $this->hasMany(Transaction::class, 'to_account_id', 'id');
    }

    public function totalDebit()
    {
        return $this->toTransactions()->sum('amount');
    }

    public function totalCredit()
    {
        return $this->fromTransactions()->sum('amount');
    }

    public function netBalance()
    {
        return $this->totalDebit() - $this->totalCredit();
    }
}
