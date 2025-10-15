<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBillTypeRequest;
use App\Models\BillType;
use App\Services\BillTypeService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BillTypeController extends Controller
{

    public function __construct(protected BillTypeService $billTypeService)
    {
        $this->middleware('can:read_bill_type')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_bill_type')->only('store', 'create');
        $this->middleware('can:update_bill_type')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.bill_type.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            BillType::withTrashed()->latest()
        )->toJson();
        return $data;
    }

    public function search(Request $request)
    {
        $reasons = $this->billTypeService->all($request->q);
        return response()->json(['data' => $reasons]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBillTypeRequest $request)
    {
        $data = $request->afterValidation();
        $reason = $this->billTypeService->store($data);
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

    public function edit($id)
    {
        $type = $this->billTypeService->show($id);
        return view('dashboard.bill_type.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBillTypeRequest $request, string $id)
    {
        $data = $request->afterValidation();
        $reason = $this->billTypeService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('bill-type.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
