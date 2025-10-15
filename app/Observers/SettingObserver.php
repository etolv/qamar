<?php

namespace App\Observers;

use App\Models\Setting;
use App\Services\ModelRecordService;

class SettingObserver
{
    /**
     * Handle the Setting "created" event.
     */
    public function created(Setting $setting): void
    {
        resolve(ModelRecordService::class)->store($setting, auth()->id(), 'create');
    }

    /**
     * Handle the Setting "updated" event.
     */
    public function updated(Setting $setting): void
    {
        resolve(ModelRecordService::class)->store($setting, auth()->id(), 'update');
    }

    /**
     * Handle the Setting "deleted" event.
     */
    public function deleted(Setting $setting): void
    {
        resolve(ModelRecordService::class)->store($setting, auth()->id(), 'delete');
    }

    /**
     * Handle the Setting "restored" event.
     */
    public function restored(Setting $setting): void
    {
        resolve(ModelRecordService::class)->store($setting, auth()->id(), 'restore');
    }

    /**
     * Handle the Setting "force deleted" event.
     */
    public function forceDeleted(Setting $setting): void
    {
        //
    }
}
