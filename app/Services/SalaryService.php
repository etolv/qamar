<?php

namespace App\Services;

use App\Models\Salary;
use Carbon\Carbon;

/**
 * Class SalaryService.
 */
class SalaryService extends BaseService
{
    public function close($id)
    {
        $salary = $this->show($id);
        $salary->update(['end_date' => Carbon::now()->format('Y-m-d')]);
        return $salary;
    }

    public function store_or_update($data)
    {
        if (isset($data['salary_id'])) {
            $salary = $this->show($data['salary_id']);
            unset($data['salary_id']);
            if ($salary->amount == $data['amount']) {
                $salary->update($data);
                return $salary;
            } else {
                $salary = $this->close($data['salary_id']);
            }
        }
        $salary = Salary::create($data);
        return $salary;
    }
}
