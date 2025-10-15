<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Rate;
use App\Http\Requests\StoreRateRequest;
use App\Http\Requests\UpdateRateRequest;
use App\Services\RateService;
use Illuminate\Http\Request;

class RateController extends Controller
{

    public function __construct(protected RateService $rateService)
    {
        $this->middleware('can:read_rate')->only('index', 'fetch', 'show');
        $this->middleware('can:create_rate')->only('store', 'create');
        $this->middleware('can:update_rate')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.rate.index');
    }

    public function fetch(Request $request)
    {
        $data = $this->rateService->fetch($request);
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRateRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Rate $rate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRateRequest $request, Rate $rate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rate $rate)
    {
        //
    }
}
