<?php

namespace App\Observers;

use App\Enums\ModelLogEnum;
use App\Models\ModelRecord;
use App\Models\Notification;
use App\Services\ModelRecordService;

class NotificationObserver
{
    /**
     * Handle the Notification "created" event.
     */
    public function created(Notification $notification): void
    {
        resolve(ModelRecordService::class)->store($notification, auth()->id(), 'create');
    }

    /**
     * Handle the Notification "updated" event.
     */
    public function updated(Notification $notification): void
    {
        resolve(ModelRecordService::class)->store($notification, auth()->id(), 'update');
    }

    /**
     * Handle the Notification "deleted" event.
     */
    public function deleted(Notification $notification): void
    {
        resolve(ModelRecordService::class)->store($notification, auth()->id(), 'delete');
    }

    /**
     * Handle the Notification "restored" event.
     */
    public function restored(Notification $notification): void
    {
        resolve(ModelRecordService::class)->store($notification, auth()->id(), 'restore');
    }

    /**
     * Handle the Notification "force deleted" event.
     */
    public function forceDeleted(Notification $notification): void
    {
        //
    }
}
