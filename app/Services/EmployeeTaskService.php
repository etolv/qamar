<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\EmployeeTask;

/**
 * Class EmployeeTaskService.
 */
class EmployeeTaskService
{
    public function update($data, $id): EmployeeTask
    {
        $employeeTask = $this->show($id);
        $employeeTask->update($data);
        return $employeeTask;
    }

    public function show($id): EmployeeTask
    {
        return EmployeeTask::findOrFail($id);
    }
}
