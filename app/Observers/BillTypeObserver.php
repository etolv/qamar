<?php

namespace App\Observers;

use App\Models\BillType;
use App\Services\AccountService;

class BillTypeObserver
{
    /**
     * Handle the BillType "created" event.
     */
    public function created(BillType $billType): void
    {
        resolve(AccountService::class)->store([
            'model_type' => BillType::class,
            'model_id' => $billType->id,
            'slug' => $billType->name,
            'name' => $billType->name,
            'is_debit' => false,
            'account_id' => resolve(AccountService::class)->fromSlug('expenses')?->id ?? null
        ]);
    }

    /**
     * Handle the BillType "updated" event.
     */
    public function updated(BillType $billType): void
    {
        //
    }

    /**
     * Handle the BillType "deleted" event.
     */
    public function deleted(BillType $billType): void
    {
        //
    }

    /**
     * Handle the BillType "restored" event.
     */
    public function restored(BillType $billType): void
    {
        //
    }

    /**
     * Handle the BillType "force deleted" event.
     */
    public function forceDeleted(BillType $billType): void
    {
        //
    }
}
