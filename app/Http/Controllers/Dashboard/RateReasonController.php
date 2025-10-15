<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRateReasonRequest;
use App\Http\Requests\UpdateRateReasonRequest;
use App\Models\RateReason;
use App\Services\RateReasonService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RateReasonController extends Controller
{

    public function __construct(protected RateReasonService $rateReasonService)
    {
        $this->middleware('can:read_rate_reason')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_rate_reason')->only('create', 'store');
        $this->middleware('can:update_rate_reason')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.rate_reason.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            RateReason::withTrashed()->latest()
        )->addColumn('rates_count', function ($item) {
            return $item->rates()->count();
        })->toJson();
        return $data;
    }

    public function search(Request $request)
    {
        $reasons = $this->rateReasonService->all($request->q);
        return response()->json(['data' => $reasons]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRateReasonRequest $request)
    {
        $data = $request->validated();
        $reason = $this->rateReasonService->store($data);
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
        $reason = $this->rateReasonService->show($id);
        return view('dashboard.rate_reason.edit', compact('reason'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRateReasonRequest $request, string $id)
    {
        $data = $request->validated();
        $reason = $this->rateReasonService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
