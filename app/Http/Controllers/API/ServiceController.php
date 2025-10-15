<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Services\ServiceService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    public function __construct(protected ServiceService $serviceService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(IndexServiceRequest $request)
    {
        $data = $request->validated();
        $services = $this->serviceService->all(data: $data, paginated: true);
        $data = ServiceResource::collection($services);
        return response()->success($data, collect($data->response()->getData()->meta ?? null)->merge($data->response()->getData()->links ?? null));
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $service = $this->serviceService->show(id: $id, withTrashed: false, withes: ['products']);
        return response()->success(ServiceResource::make($service));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
