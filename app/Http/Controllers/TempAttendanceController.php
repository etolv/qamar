<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportAttendanceRequest;
use App\Models\TempAttendance;
use App\Services\TempAttendanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Intervention\Image\Colors\Rgb\Channels\Red;
use Yajra\DataTables\Facades\DataTables;

class TempAttendanceController extends Controller
{

    public function __construct(protected TempAttendanceService $tempAttendanceService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        session()->flash('warning', _t('Data in this table is valid for one hour, and it content does not affect the real data !'));
        return view('dashboard.attendance.temp.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            TempAttendance::with([
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
            })->latest()
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

    public function import(ImportAttendanceRequest $request)
    {
        $data = $request->validated();
        $attendance = $this->tempAttendanceService->import($data);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    public function clear()
    {
        $cleared = $this->tempAttendanceService->clear();
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
