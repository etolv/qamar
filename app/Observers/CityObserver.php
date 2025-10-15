<?php

namespace App\Observers;

use App\Enums\ModelLogEnum;
use App\Models\City;
use App\Models\ModelRecord;
use App\Services\ModelRecordService;

class CityObserver
{
    /**
     * Handle the City "created" event.
     */
    public function created(City $city): void
    {
        resolve(ModelRecordService::class)->store($city, auth()->id(), 'create');
    }

    /**
     * Handle the City "updated" event.
     */
    public function updated(City $city): void
    {
        resolve(ModelRecordService::class)->store($city, auth()->id(), 'update');
    }

    /**
     * Handle the City "deleted" event.
     */
    public function deleted(City $city): void
    {
        resolve(ModelRecordService::class)->store($city, auth()->id(), 'delete');
    }

    /**
     * Handle the City "restored" event.
     */
    public function restored(City $city): void
    {
        resolve(ModelRecordService::class)->store($city, auth()->id(), 'restore');
    }

    /**
     * Handle the City "force deleted" event.
     */
    public function forceDeleted(City $city): void
    {
        //
    }
}
