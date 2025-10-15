<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDriverProfileReqeust;
use App\Http\Requests\UpdateDriverProfileRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Http\Resources\DriverResource;
use App\Services\DriverService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProfileController extends Controller
{

    public function __construct(protected DriverService $driverService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $driver = $this->driverService->show(id: auth()->user()->type_id, withes: ['city', 'branch', 'nationality']);
        return response()->success(DriverResource::make($driver));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UpdateDriverProfileRequest $request)
    {
        $data = $request->afterValidation();
        $driver = $this->driverService->update($data, id: $data['driver_id']);
        return response()->success(DriverResource::make($driver));
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
