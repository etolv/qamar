<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexCityRequest;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Services\CityService;
use Illuminate\Http\Request;

class CityController extends Controller
{

    public function __construct(private CityService $cityService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(IndexCityRequest $request)
    {
        $data = $request->validated();
        $cities = $this->cityService->all(data: $data, paginated: true);
        $data = CityResource::collection($cities);
        return response()->success($data, collect($data->response()->getData()->meta ?? null)->merge($data->response()->getData()->links ?? null));
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
    public function show(City $city)
    {
        return response()->success(CityResource::make($city));
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
