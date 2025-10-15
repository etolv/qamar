<?php

namespace App\Observers;

use App\Models\Package;
use App\Services\ModelRecordService;

class PackageObserver
{
    /**
     * Handle the Package "created" event.
     */
    public function created(Package $package): void
    {
        resolve(ModelRecordService::class)->store($package, auth()->id(), 'create');
    }

    /**
     * Handle the Package "updated" event.
     */
    public function updated(Package $package): void
    {
        resolve(ModelRecordService::class)->store($package, auth()->id(), 'update');
    }

    /**
     * Handle the Package "deleted" event.
     */
    public function deleted(Package $package): void
    {
        resolve(ModelRecordService::class)->store($package, auth()->id(), 'delete');
    }

    /**
     * Handle the Package "restored" event.
     */
    public function restored(Package $package): void
    {
        resolve(ModelRecordService::class)->store($package, auth()->id(), 'restore');
    }

    /**
     * Handle the Package "force deleted" event.
     */
    public function forceDeleted(Package $package): void
    {
        //
    }
}
