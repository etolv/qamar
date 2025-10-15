<?php

namespace App\Services;

use App\Enums\VacationStatusEnum;
use App\Enums\VacationTypeEnum;
use App\Models\Vacation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class VacationService.
 */
class VacationService
{

    public function __construct(protected EmployeeService $employeeService) {}
    public function store($data)
    {
        if (isset($data['all_employees'])) {
            $data['employees'] = $this->employeeService->all()->pluck('type_id')->toArray();
            unset($data['all_employees']);
        }
        foreach ($data['employees'] as $employee_id) {
            $data['employee_id'] = $employee_id;
            $vacation = Vacation::create(Arr::except($data, ['file', 'employees']));
            if (isset($data['file'])) {
                $vacation->addMedia($data['file'])->toMediaCollection('file');
            }
        }
        return $vacation;
    }

    public function show($id)
    {
        return Vacation::with([
            'employee' => function ($query) {
                $query->with([
                    'user' => function ($query) {
                        $query->withTrashed();
                    }
                ]);
            }
        ])->find($id);
    }

    public function update($data, $id)
    {
        $vacation = $this->show($id);
        $vacation->update(Arr::except($data, ['file']));
        if (isset($data['file'])) {
            $vacation->clearMediaCollection('file');
            $vacation->addMedia($data['file'])->toMediaCollection('file');
        }
        return $vacation;
    }
}
