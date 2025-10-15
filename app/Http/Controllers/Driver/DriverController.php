<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDriverLocationRequest;
use App\Services\DriverService;
use Illuminate\Http\Request;

class DriverController extends Controller
{

    public function __construct(private DriverService $driverService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function update_location(UpdateDriverLocationRequest $request)
    {
        $data = $request->afterValidation();
        $driver = $this->driverService->updateLocation($data);
        return response()->success();
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
