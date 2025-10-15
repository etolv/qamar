<?php

namespace App\Observers;

use App\Models\Branch;
use App\Services\ModelRecordService;

class BranchObserver
{
    /**
     * Handle the Branch "created" event.
     */
    public function created(Branch $branch): void
    {
        resolve(ModelRecordService::class)->store($branch, auth()->id(), 'create');
    }

    /**
     * Handle the Branch "updated" event.
     */
    public function updated(Branch $branch): void
    {
        resolve(ModelRecordService::class)->store($branch, auth()->id(), 'update');
    }

    /**
     * Handle the Branch "deleted" event.
     */
    public function deleted(Branch $branch): void
    {
        resolve(ModelRecordService::class)->store($branch, auth()->id(), 'delete');
    }

    /**
     * Handle the Branch "restored" event.
     */
    public function restored(Branch $branch): void
    {
        resolve(ModelRecordService::class)->store($branch, auth()->id(), 'restore');
    }

    /**
     * Handle the Branch "force deleted" event.
     */
    public function forceDeleted(Branch $branch): void
    {
        //
    }
}
