<?php

namespace App\Observers;

use App\Enums\ModelLogEnum;
use App\Models\Category;
use App\Models\ModelRecord;
use App\Services\ModelRecordService;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        resolve(ModelRecordService::class)->store($category, auth()->id(), 'create');
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        resolve(ModelRecordService::class)->store($category, auth()->id(), 'update');
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        resolve(ModelRecordService::class)->store($category, auth()->id(), 'delete');
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        resolve(ModelRecordService::class)->store($category, auth()->id(), 'restore');
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        //
    }
}
