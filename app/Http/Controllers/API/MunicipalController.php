<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexMunicipalRequest;
use App\Http\Resources\MunicipalResource;
use App\Models\Municipal;
use App\Services\MunicipalService;
use Illuminate\Http\Request;

class MunicipalController extends Controller
{

    public function __construct(private MunicipalService $municipalService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(IndexMunicipalRequest $request)
    {
        $data = $request->validated();
        $municipals = $this->municipalService->all(data: $data, paginated: true);
        $data = MunicipalResource::collection($municipals);
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
    public function show(Municipal $municipal)
    {
        return response()->success(MunicipalResource::make($municipal));
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
