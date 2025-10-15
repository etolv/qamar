<?php

namespace App\Observers;

use App\Services\ModelRecordService;
use Spatie\Permission\Models\Role;

class RoleObserver
{
    /**
     * Handle the Role "created" event.
     */
    public function created(Role $role): void
    {
        resolve(ModelRecordService::class)->store($role, auth()->id(), 'create');
    }

    /**
     * Handle the Role "updated" event.
     */
    public function updated(Role $role): void
    {
        resolve(ModelRecordService::class)->store($role, auth()->id(), 'update');
    }

    /**
     * Handle the Role "deleted" event.
     */
    public function deleted(Role $role): void
    {
        resolve(ModelRecordService::class)->store($role, auth()->id(), 'delete');
    }

    /**
     * Handle the Role "restored" event.
     */
    public function restored(Role $role): void
    {
        resolve(ModelRecordService::class)->store($role, auth()->id(), 'restore');
    }

    /**
     * Handle the Role "force deleted" event.
     */
    public function forceDeleted(Role $role): void
    {
        //
    }
}
