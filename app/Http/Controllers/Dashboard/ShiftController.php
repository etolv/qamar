<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeShiftRequest;
use App\Http\Requests\StoreShiftRequest;
use App\Http\Requests\UpdateEmployeeShiftRequest;
use App\Http\Requests\UpdateShiftRequest;
use App\Models\Shift;
use App\Services\ShiftService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Yajra\DataTables\Facades\DataTables;

class ShiftController extends Controller
{

    public function __construct(protected ShiftService $shiftService)
    {
        $this->middleware('can:read_shift')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_shift')->only('create', 'store', 'duplicate');
        $this->middleware('can:update_shift')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.shift.index');
    }

    public function fetch()
    {
        $data = DataTables::eloquent(
            Shift::withTrashed()->with([
                'employees'
            ])->latest()
        )->editColumn('type', function ($item) {
            return _t($item->type->name);
        })->editColumn('holiday', function ($item) {
            return _t($item->holiday->name);
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }

    public function search(Request $request)
    {
        $shifts = $this->shiftService->all($request->q);
        return response()->json(['data' => $shifts]);
    }

    public function create()
    {
        $shift = null;
        return view('dashboard.shift.add', compact('shift'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShiftRequest $request)
    {
        $data = $request->afterValidation();
        $shift = $this->shiftService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('shift.index');
    }

    public function duplicate($id)
    {
        $shift = $this->shiftService->show($id);
        return view('dashboard.shift.add', compact('shift'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $shift = $this->shiftService->show($id);
        $employee_ids = $shift->employees->pluck('id')->toArray();
        return view('dashboard.shift.show', compact('shift', 'employee_ids'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShiftRequest $request, string $id)
    {
        $data = $request->afterValidation();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
