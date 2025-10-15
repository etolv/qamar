<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Services\BrandService;
use Illuminate\Http\Request;

class BrandController extends Controller
{

    public function __construct(private BrandService $brandService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = $this->brandService->all();
        $data = BrandResource::collection($brands);
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
    public function show(string $id)
    {
        $brand = $this->brandService->show($id);
        return response()->success(BrandResource::make($brand));
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
