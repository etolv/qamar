<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Services\TripService;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function __construct(private TripService $tripService) {}

    public function index(IndexTripRequest $request)
    {
        return TripResource::collection($this->tripService->all(data: $request->validated(), paginated: true));
    }

    public function show($id)
    {
        return TripResource::make($this->tripService->show(id: $id, withes: ['tripable']));
    }

    public function update(UpdateTripRequest $request, $id)
    {
        $data = $request->validated();
        $trip = $this->tripService->update(data: $data, id: $id);
        return TripResource::make($trip);
    }
}
