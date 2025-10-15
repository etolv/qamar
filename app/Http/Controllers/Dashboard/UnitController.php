<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUnitRequest;
use App\Models\Unit;
use App\Services\UnitService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class UnitController extends Controller
{

    public function __construct(protected UnitService $unitService)
    {
        $this->middleware('can:read_unit')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_unit')->only('store', 'create');
        $this->middleware('can:update_unit')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.unit.index');
    }

    public function search(Request $request)
    {
        $countries = $this->unitService->all($request->q);
        return response()->json(['data' => $countries]);
    }

    public function fetch()
    {
        $data = DataTables::eloquent(
            Unit::withTrashed()->with([
                'parent' => function ($query) {
                    $query->withTrashed();
                }
            ])->latest()
        )->toJson();
        return $data;
    }

    public function create()
    {
        return view('dashboard.unit.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUnitRequest $request)
    {
        $data = $request->validated();
        $unit = $this->unitService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('unit.index');
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
        $unit = $this->unitService->show($id);
        return view('dashboard.unit.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreUnitRequest $request, string $id)
    {
        $data = $request->validated();
        $unit = $this->unitService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('unit.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
