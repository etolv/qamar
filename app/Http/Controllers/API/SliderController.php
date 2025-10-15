<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SliderResource;
use App\Services\SliderService;
use Illuminate\Http\Request;

class SliderController extends Controller
{

    public function  __construct(private SliderService $sliderService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sliders = $this->sliderService->all();
        return response()->success(SliderResource::collection($sliders));
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
