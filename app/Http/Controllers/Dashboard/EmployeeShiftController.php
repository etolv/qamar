<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\DestroyEmployeeShiftRequest;
use App\Http\Requests\StoreEmployeeShiftRequest;
use App\Http\Requests\UpdateEmployeeShiftRequest;
use App\Models\EmployeeShift;
use App\Services\EmployeeShiftService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class EmployeeShiftController extends Controller
{

    public function __construct(protected EmployeeShiftService $employeeShiftService)
    {
        $this->middleware('can:read_shift')->only('index', 'show', 'search');
        $this->middleware('can:create_shift')->only('create', 'store');
        $this->middleware('can:update_shift')->only('edit', 'update');
        $this->middleware('can:delete_shift')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.shift.employee.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            EmployeeShift::with([
                'employee.user' => function ($query) {
                    $query->withTrashed();
                },
                'shift' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->employee_id, function ($query) use ($request) {
                $query->where('employee_id', $request->employee_id);
            })->when($request->shift_id, function ($query) use ($request) {
                $query->where('shift_id', $request->shift_id);
            })->when($request->date && str_contains($request->date, ' to '), function ($query) use ($request) {
                [$from, $to] = explode(' to ', $request->date);
                $query->whereBetween('date', [$from, $to]);
            })->latest('date')
        )->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->editColumn('date', function ($item) {
            return Carbon::parse($item->date)->format('Y-m-d D');
        })->addColumn('employee_image', function ($item) {
            return $item->employee->user->getFirstMediaUrl('profile');
        })->toJson();
        return $data;
    }

    public function create()
    {
        return view('dashboard.shift.employee.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeShiftRequest $request)
    {
        $data = $request->afterValidation();
        $employeeShift = $this->employeeShiftService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('employee-shift.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit($id)
    {
        $employee_shift = $this->employeeShiftService->show($id);
        return view('dashboard.shift.employee.edit', compact('employee_shift'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeShiftRequest $request, string $id)
    {
        $data = $request->afterValidation();
        $employee_shift = $this->employeeShiftService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('employee-shift.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyEmployeeShiftRequest $request, string $id)
    {
        $data = $request->afterValidation($id);
        $deleted = $this->employeeShiftService->destroy($id, true);
        if ($deleted) {
            session()->flash('message', _t('Success'));
        } else {
            session()->flash('error', _t('Something went wrong, please try again'));
        }
        return redirect()->back();
    }
}
