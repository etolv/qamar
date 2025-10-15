<?php

namespace App\Observers;

use App\Enums\ModelLogEnum;
use App\Models\ModelRecord;
use App\Models\State;
use App\Services\ModelRecordService;

class StateObserver
{
    /**
     * Handle the State "created" event.
     */
    public function created(State $state): void
    {
        resolve(ModelRecordService::class)->store($state, auth()->id(), 'create');
    }

    /**
     * Handle the State "updated" event.
     */
    public function updated(State $state): void
    {
        resolve(ModelRecordService::class)->store($state, auth()->id(), 'update');
    }

    /**
     * Handle the State "deleted" event.
     */
    public function deleted(State $state): void
    {
        resolve(ModelRecordService::class)->store($state, auth()->id(), 'delete');
    }

    /**
     * Handle the State "restored" event.
     */
    public function restored(State $state): void
    {
        resolve(ModelRecordService::class)->store($state, auth()->id(), 'restore');
    }

    /**
     * Handle the State "force deleted" event.
     */
    public function forceDeleted(State $state): void
    {
        //
    }
}
