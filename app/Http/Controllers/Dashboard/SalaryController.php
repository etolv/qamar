<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\CloseSalaryRequest;
use App\Http\Requests\StoreSalaryRequest;
use App\Models\Salary;
use App\Services\EmployeeService;
use App\Services\SalaryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SalaryController extends Controller
{

    public function  __construct(protected SalaryService $salaryService, protected EmployeeService $employeeService)
    {
        $this->middleware('can:read_salary')->only('index', 'fetch', 'show');
        $this->middleware('can:create_salary')->only('create', 'store', 'import');
        $this->middleware('can:update_salary')->only('edit', 'update');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = $this->employeeService->without_salaries();
        if (count($employees)) {
            session()->flash('warning', _t('You need to add salary for employees') . ' :' . implode(', ', $employees->pluck('user.name')->toArray()));
        }
        return view('dashboard.salary.index');
    }

    public function close(CloseSalaryRequest $request, $id)
    {
        $data = $request->afterValidation($id);
        $salary = $this->salaryService->close($id);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Salary::with([
                'employee' => function ($query) {
                    $query->with([
                        'user' => function ($query) {
                            $query->withTrashed();
                        }
                    ]);
                }
            ])->when($request->date && str_contains($request->date, ' to '), function ($query) use ($request) {
                [$from, $to] = explode(' to ', $request->date);
                $query->whereBetween('start_date', [$from, $to])
                    ->orWhereBetween('end_date', [$from, $to]);
            })->when($request->employee_id, function ($query) use ($request) {
                $query->where('employee_id', $request->employee_id);
            })->when($request->user_branch_id, function ($query) use ($request) {
                $query->whereHas('employee', function ($subQuery) use ($request) {
                    $subQuery->where('branch_id', $request->user_branch_id);
                });
            })->latest()
        )->addColumn('employee_image', function ($item) {
            return $item->employee?->user?->getFirstMediaUrl('profile');
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }

    public function create(Request $request)
    {
        $employee = null;
        $salary = null;
        if ($request->employee_id) {
            $employee = $this->employeeService->show($request->employee_id);
            if ($employee) {
                $salary = $this->employeeService->salary($employee->id);
            }
        }
        return view('dashboard.salary.add', compact('employee', 'salary'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalaryRequest $request)
    {
        $data = $request->afterValidation();
        $salary = $this->salaryService->store_or_update($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('salary.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
