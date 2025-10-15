<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexPackageApiRequest;
use App\Http\Resources\PackageResource;
use App\Services\PackageService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PackageController extends Controller
{

    public function __construct(private PackageService $packageService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(IndexPackageApiRequest $request)
    {
        $data = $request->afterValidation();
        $packages = $this->packageService->all(paginate: true, data: $data);
        $data = PackageResource::collection($packages);
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
        $package = $this->packageService->show(id: $id);
        return response()->success(PackageResource::make($package));
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
