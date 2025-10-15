<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportAttendanceRequest;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Google\Rpc\PreconditionFailure\Violation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{

    public function __construct(protected AttendanceService $attendanceService)
    {
        $this->middleware('can:read_attendance')->only('index', 'show');
        $this->middleware('can:create_attendance')->only('create', 'store', 'import');
        $this->middleware('can:update_attendance')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.attendance.index');
    }

    public function import(ImportAttendanceRequest $request)
    {
        $data = $request->validated();
        $attendance = $this->attendanceService->import($data);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Attendance::with([
                'employee' => function ($query) {
                    $query->with([
                        'user' => function ($query) {
                            $query->withTrashed();
                        }
                    ]);
                },
                'shift' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->date && str_contains($request->date, ' to '), function ($query) use ($request) {
                [$from, $to] = explode(' to ', $request->date);
                $query->whereBetween('date', [$from, $to]);
            })->when($request->employee_id, function ($query) use ($request) {
                $query->where('employee_id', $request->employee_id);
            })->when($request->shift_id, function ($query) use ($request) {
                $query->where('shift_id', $request->shift_id);
            })->when($request->overtime, function ($query) use ($request) {
                $query->where('extra_hours', '>', 0);
            })->when($request->user_branch_id, function ($query) use ($request) {
                $query->whereHas('employee', function ($subQuery) use ($request) {
                    $subQuery->where('branch_id', $request->user_branch_id);
                });
            })
        )->addColumn('employee_image', function ($item) {
            return $item->employee?->user?->getFirstMediaUrl('profile');
        })->addColumn('overtime_status_name', function ($item) {
            if ($item->extra_hours > 0) {
                return _t($item->overtime_status->name);
            }
        })->addColumn('status_name', function ($item) {
            return _t($item->status->name);
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }

    public function create()
    {
        return view('dashboard.attendance.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRequest $request)
    {
        $data = $request->afterValidation();
        $attendance = $this->attendanceService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('attendance.index');
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
        $attendance = $this->attendanceService->show($id);
        return view('dashboard.attendance.edit', compact('attendance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttendanceRequest $request, string $id)
    {
        $data = $request->afterValidation();
        $attendance = $this->attendanceService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('attendance.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
