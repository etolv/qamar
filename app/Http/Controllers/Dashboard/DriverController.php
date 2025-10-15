<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\Driver;
use App\Models\User;
use App\Services\DriverService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DriverController extends Controller
{

    public function __construct(protected DriverService $driverService)
    {
        $this->middleware('can:read_driver')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_driver')->only('store', 'create');
        $this->middleware('can:update_driver')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.driver.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            User::withTrashed()->with([
                'account.trips',
            ])->onlyDrivers()->latest()
        )->addColumn('profile_image', function ($item) {
            return $item->getFirstMedia('profile') ? $item->getFirstMedia('profile')->getUrl() : null;
        })->editColumn('phone', function ($item) {
            return $item->dial_code . $item->phone;
        })->toJson();
        return $data;
    }

    public function search(Request $request)
    {
        $employees = $this->driverService->all(search: $request->q, type: $request->type ?? null, excluded_ids: $request->excluded_ids ?? []);
        return response()->json(['data' => $employees]);
    }

    public function create()
    {
        return view('dashboard.driver.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDriverRequest $request)
    {
        $data = $request->afterValidation();
        $driver = $this->driverService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('driver.index');
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
        $driver = $this->driverService->show($id);
        return view('dashboard.driver.edit', compact('driver'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDriverRequest $request, string $id)
    {
        $data = $request->afterValidation();
        $driver = $this->driverService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('driver.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
