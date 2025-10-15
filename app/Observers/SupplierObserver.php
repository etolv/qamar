<?php

namespace App\Observers;

use App\Enums\ModelLogEnum;
use App\Models\ModelRecord;
use App\Models\Supplier;
use App\Services\AccountService;
use App\Services\ModelRecordService;

class SupplierObserver
{
    /**
     * Handle the Supplier "created" event.
     */
    public function created(Supplier $supplier): void
    {
        resolve(ModelRecordService::class)->store($supplier, auth()->id(), 'create');
        resolve(AccountService::class)->store([
            'model_type' => Supplier::class,
            'model_id' => $supplier->id,
            'slug' => $supplier->name ?? $supplier->company,
            'name' => $supplier->name ?? $supplier->company,
            'is_debit' => false,
            'account_id' => resolve(AccountService::class)->fromSlug('supplier')?->id ?? null
        ]);
    }

    /**
     * Handle the Supplier "updated" event.
     */
    public function updated(Supplier $supplier): void
    {
        resolve(ModelRecordService::class)->store($supplier, auth()->id(), 'update');
    }

    /**
     * Handle the Supplier "deleted" event.
     */
    public function deleted(Supplier $supplier): void
    {
        resolve(ModelRecordService::class)->store($supplier, auth()->id(), 'delete');
    }

    /**
     * Handle the Supplier "restored" event.
     */
    public function restored(Supplier $supplier): void
    {
        resolve(ModelRecordService::class)->store($supplier, auth()->id(), 'restore');
    }

    /**
     * Handle the Supplier "force deleted" event.
     */
    public function forceDeleted(Supplier $supplier): void
    {
        //
    }
}
