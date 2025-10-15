<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\VacationStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVacationRequest;
use App\Http\Requests\RoundVacationRequest;
use App\Http\Requests\StoreVacationRequest;
use App\Http\Requests\UpdateVacationStatusRequest;
use App\Models\Vacation;
use App\Services\EmployeeService;
use App\Services\VacationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VacationController extends Controller
{

    public function __construct(private VacationService $vacationService, private EmployeeService $employeeService)
    {
        $this->middleware('can:read_vacation')->only('index', 'show');
        $this->middleware('can:create_vacation')->only('import');
        // $this->middleware('can:create_vacation')->only('create', 'store', 'import');
        $this->middleware('can:update_vacation')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.vacation.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Vacation::with([
                'employee' => function ($query) {
                    $query->with([
                        'user' => function ($query) {
                            $query->withTrashed();
                        }
                    ]);
                }
            ])->when($request->date && str_contains($request->date, ' to '), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    [$from, $to] = explode(' to ', $request->date);
                    $query->whereBetween('start_date', [$from, $to])
                        ->orWhereBetween('end_date', [$from, $to]);
                });
            })->when($request->employee_id, function ($query) use ($request) {
                $query->where('employee_id', $request->employee_id);
            })->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })->when($request->user_branch_id, function ($query) use ($request) {
                $query->whereHas('employee', function ($subQuery) use ($request) {
                    $subQuery->where('branch_id', $request->user_branch_id);
                });
            })->latest()
        )->addColumn('employee_image', function ($item) {
            return $item->employee?->user?->getFirstMediaUrl('profile');
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->editColumn('approved_at', function ($item) {
            return Carbon::parse($item->approved_at)->format('Y-m-d H:i');
        })->addColumn('status_name', function ($item) {
            return _t($item->status?->name);
        })->addColumn('type_name', function ($item) {
            return _t($item->type?->name);
        })->addColumn('file', function ($item) {
            return $item->getFirstMediaUrl('file');
        })->toJson();
        return $data;
    }

    public function round(RoundVacationRequest $request, $employee_id)
    {
        $data = $request->afterValidation($employee_id);
        $vacation = $this->vacationService->store($data);
        session()->flash('message', _t("Success"));
        return redirect()->back();
    }

    public function update_status($id, UpdateVacationStatusRequest $request)
    {
        $data = $request->afterValidation($id);
        $vacation = $this->vacationService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    public function create(CreateVacationRequest $request)
    {
        $data = $request->afterValidation();
        $employee = null;
        if (isset($data['employee_id'])) {
            $employee = $this->employeeService->show($data['employee_id']);
        }
        return view('dashboard.vacation.add', compact('employee'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVacationRequest $request)
    {
        $data = $request->afterValidation();
        $vacation = $this->vacationService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->back();
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
