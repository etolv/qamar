<?php

namespace App\Services;

use App\Enums\SectionEnum;
use App\Imports\EmployeeImport;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

/**
 * Class EmployeeService.
 */
class EmployeeService
{

    public function __construct(protected UserService $userService) {}

    public function stats()
    {
        return [
            'management' => Employee::whereHas('job', function ($query) {
                $query->where('section', SectionEnum::MANAGEMENT->value);
            })->count(),
            'procurement' => Employee::whereHas('job', function ($query) {
                $query->where('section', SectionEnum::PROCUREMENT->value);
            })->count(),
            'sales' => Employee::whereHas('job', function ($query) {
                $query->where('section', SectionEnum::SALES->value);
            })->count(),
            'staff' => Employee::whereHas('job', function ($query) {
                $query->where('section', SectionEnum::STAFF->value);
            })->count(),
            'financial' => Employee::whereHas('job', function ($query) {
                $query->where('section', SectionEnum::FINANCIAL->value);
            })->count(),
            'warehouse' => Employee::whereHas('job', function ($query) {
                $query->where('section', SectionEnum::WAREHOUSE->value);
            })->count(),
        ];
    }
    public function without_salaries()
    {
        $employees = Employee::with('user')->withoutSalaries()->get();
        return $employees;
    }

    public function without_employee_no()
    {
        $employees = Employee::with('user')->where('employee_no', null)->get();
        return $employees;
    }

    public function import($data)
    {
        Excel::import(new EmployeeImport, $data['file']);
        return true;
    }

    public function store($data)
    {
        $role = Role::find($data['role_id']);
        unset($data['role_id']);
        $user = User::create(Arr::only($data, ['password', 'name', 'phone', 'email']));
        if (isset($data['image'])) {
            $user->clearMediaCollection('profile');
            $user->addMedia($data['image'])->toMediaCollection('profile');
        }
        // TODO send password to user email
        $user->assignRole($role);
        $employee = new Employee(Arr::except($data, ['password', 'name', 'phone', 'email', 'image']));
        // $employee = new Employee(Arr::only($data, ['city_id', 'branch_id', 'job_id', 'nationality_id', 'vacation_days', 'remaining_vacation_days']));
        $employee->user()->associate($user);
        $employee->save();
        $user->account()->associate($employee);
        $user->save();
        if (isset($data['employee_infos'])) {
            foreach ($data['employee_infos'] as $employee_info) {
                $info = $employee->employee_infos()->create(Arr::except($employee_info, ['file']));
                if (isset($employee_info['file'])) {
                    $info->addMedia($employee_info['file'])->toMediaCollection('file');
                }
            }
        }
        // resolve(AccountService::class)->store([
        //     'model_type' => Employee::class,
        //     'model_id' => $employee->id,
        //     'slug' => $user->name,
        //     'name' => $user->name,
        //     'is_debit' => false,
        //     'account_id' => resolve(AccountService::class)->fromSlug('client')?->id ?? null
        // ]);
        return $employee;
    }

    public function update($data, $id)
    {
        DB::beginTransaction();
        $employee = $this->show($id);
        $user = $employee->user;
        if (isset($data['image'])) {
            $user->clearMediaCollection('profile');
            $user->addMedia($data['image'])->toMediaCollection('profile');
            unset($data['image']);
        }
        if (isset($data['password']) && $data['password'])
            $data['password'] = Hash::make($data['password']);
        else
            $data['password'] = $user->password;
        if (isset($data['role_id'])) {
            $user->roles()->detach();
            $role = Role::find($data['role_id']);
            $user->assignROle($role);
            unset($data['role_id']);
        }
        $user->update($data);
        $employee->update((Arr::except($data, ['password', 'name', 'phone', 'email', 'image'])));
        if (isset($data['vacation_days'])) {
            $taken_days = $employee->used_vacation_days;
            $employee->update(['remaining_vacation_days' => $data['vacation_days'] - $taken_days]);
        }
        if (isset($data['employee_infos'])) {
            $employee->employeeInfos()->whereNotIn('id', array_column($data['employee_infos'], 'id'))->delete();
            foreach ($data['employee_infos'] as $employee_info) {
                $info = $employee->employeeInfos()->updateOrCreate(['id' => $employee_info['id'] ?? null], Arr::except($employee_info, ['file']));
                if (isset($employee_info['file'])) {
                    $info->clearMediaCollection('file');
                    $info->addMedia($employee_info['file'])->toMediaCollection('file');
                }
            }
        } else {
            $employee->employeeInfos()->delete();
        }
        DB::commit();
        return $employee;
    }

    public function salary($employee_id)
    {
        $employee = $this->show($employee_id);
        return $employee->salaries()->latest()->first();
    }

    public function show($id)
    {
        return Employee::with(['user' => function ($query) {
            $query->withTrashed();
        }])->find($id);
    }

    public function all($search = null, $type = null, $paginated = false, $excluded_ids = [])
    {
        $query = User::when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('phone', 'like', "%$search%");
            });
        })->when($type, function ($query) use ($type) {
            $query->whereHas('roles', function ($query) use ($type) {
                $query->where('name', 'like', "%$type%");
            });
        })->when($excluded_ids, function ($query) use ($excluded_ids) {
            $query->whereNotIn('type_id', $excluded_ids);
        })->when(request()->user_branch_id, function ($query) {
            $query->whereHasMorph('account', [Employee::class], function ($query) {
                $query->where('branch_id', request()->user_branch_id);
            });
        })->onlyEmployees()->latest();
        if ($paginated)
            return $query->paginate();
        return $query->get();
    }
}
