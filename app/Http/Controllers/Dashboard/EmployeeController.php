<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportEmployeeRequest;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeSettingRequest;
use App\Models\City;
use App\Models\Employee;
use App\Models\User;
use App\Services\EmployeeService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{

    public function __construct(protected EmployeeService $employeeService, protected SettingService $settingService)
    {
        $this->middleware('can:read_employee')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_employee')->only('store', 'create');
        $this->middleware('can:update_employee')->only('update', 'edit', 'settings', 'update_settings');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statistics = $this->employeeService->stats();
        $employees = $this->employeeService->without_employee_no();
        if (count($employees)) {
            session()->flash('warning', _t('employee_fingerprint_id_warning') . " " . implode(', ', $employees->pluck('user.name')->toArray()));
        }
        return view('dashboard.employee.index', compact('statistics'));
    }

    public function import(ImportEmployeeRequest $request)
    {
        $data = $request->validated();
        $employees = $this->employeeService->import($data);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            User::withTrashed()->with([
                'account.job' => function ($query) {
                    $query->withTrashed();
                },
                'account.nationality' => function ($query) {
                    $query->withTrashed();
                },
            ])->when($request->job_id, function ($query) use ($request) {
                $query->where('job_id', $request->job_id);
            })->when($request->nationality_id, function ($query) use ($request) {
                $query->whereHasMorph('account', [Employee::class], function ($query) use ($request) {
                    $query->where('nationality_id', $request->nationality_id);
                });
            })->when($request->role_id, function ($query) use ($request) {
                $query->whereHas('roles', function ($query) use ($request) {
                    $query->where('id', $request->role_id);
                });
            })->when($request->section, function ($query) use ($request) {
                $query->whereHasMorph('account', [Employee::class], function ($query) use ($request) {
                    $query->whereHas('job', function ($query) use ($request) {
                        $query->where('section', $request->section);
                    });
                });
            })->onlyEmployees()->latest()
        )->addColumn('profile_image', function ($item) {
            return $item->getFirstMedia('profile') ? $item->getFirstMedia('profile')->getUrl() : null;
        })->addColumn('role', function ($item) {
            $role = $item->roles()->first();
            return $role?->name;
        })->addColumn('holiday_name', function ($item) {
            return $item->type->holiday?->name;
        })->editColumn('phone', function ($item) {
            return $item->dial_code . $item->phone;
        })->toJson();
        return $data;
    }

    public function settings()
    {
        $settings = $this->settingService->all(keys: ['employee_minimum_profit', 'employee_profit_percentage']);
        return view('dashboard.employee.setting', compact('settings'));
    }

    public function update_settings(UpdateEmployeeSettingRequest $request)
    {
        $data = $request->afterValidation();
        foreach ($data as $key => $value) {
            $setting = $this->settingService->updateFromKey($key, $value, 'NUMERIC');
        }
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    public function search(Request $request)
    {
        $employees = $this->employeeService->all(search: $request->q, type: $request->type ?? null, excluded_ids: $request->excluded_ids ?? []);
        return response()->json(['data' => $employees]);
    }

    public function create()
    {
        $roles = Role::get();
        return view('dashboard.employee.add', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->afterValidation();
        $result = $this->employeeService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('employee.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = $this->employeeService->show($id);
        $hourly_rate = 0;
        if ($employee->activeSalary)
            $hourly_rate = $employee->activeSalary?->amount / $employee->activeSalary?->working_hours ?? 0;
        return view('dashboard.employee.show', compact('employee', 'hourly_rate'));
    }

    public function edit($id)
    {
        $employee = $this->employeeService->show($id);
        $roles = Role::get();
        return view('dashboard.employee.edit', compact('employee', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, string $id)
    {
        $data = $request->afterValidation();
        $employee = $this->employeeService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('employee.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
