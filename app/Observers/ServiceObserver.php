<?php

namespace App\Observers;

use App\Models\Service;
use App\Services\ModelRecordService;

class ServiceObserver
{
    /**
     * Handle the Service "created" event.
     */
    public function created(Service $service): void
    {
        resolve(ModelRecordService::class)->store($service, auth()->id(), 'create');
    }

    /**
     * Handle the Service "updated" event.
     */
    public function updated(Service $service): void
    {
        resolve(ModelRecordService::class)->store($service, auth()->id(), 'update');
    }

    /**
     * Handle the Service "deleted" event.
     */
    public function deleted(Service $service): void
    {
        resolve(ModelRecordService::class)->store($service, auth()->id(), 'delete');
    }

    /**
     * Handle the Service "restored" event.
     */
    public function restored(Service $service): void
    {
        resolve(ModelRecordService::class)->store($service, auth()->id(), 'restore');
    }

    /**
     * Handle the Service "force deleted" event.
     */
    public function forceDeleted(Service $service): void
    {
        //
    }
}
