<?php

namespace App\Observers;

use App\Models\Employee;
use App\Services\AccountService;

class EmployeeObserver
{
    /**
     * Handle the Employee "created" event.
     */
    public function created(Employee $employee): void
    {
        resolve(AccountService::class)->store([
            'model_type' => Employee::class,
            'model_id' => $employee->id,
            'slug' => $employee->user->name,
            'name' => $employee->user->name,
            'is_debit' => false,
            'account_id' => resolve(AccountService::class)->fromSlug('employee')?->id ?? null
        ]);
    }

    /**
     * Handle the Employee "updated" event.
     */
    public function updated(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "deleted" event.
     */
    public function deleted(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "restored" event.
     */
    public function restored(Employee $employee): void
    {
        //
    }

    /**
     * Handle the Employee "force deleted" event.
     */
    public function forceDeleted(Employee $employee): void
    {
        //
    }
}
