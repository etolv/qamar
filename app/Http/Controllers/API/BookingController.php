<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexBookingRequest;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\StoreMobileBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Services\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{

    public function __construct(private BookingService $bookingService)
    {
        $this->middleware('can:update_booking')->only('update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(IndexBookingRequest $request)
    {
        $data = $request->validated();
        $bookings = $this->bookingService->all(data: $data, paginated: true, withes: ['addressModel']);
        $data = BookingResource::collection($bookings);
        return response()->success($data, collect($data->response()->getData()->meta ?? null)->merge($data->response()->getData()->links ?? null));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMobileBookingRequest $request)
    {
        $data = $request->validated();
        $booking = $this->bookingService->store($data);
        return response()->success(BookingResource::make($booking));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $booking = $this->bookingService->show($id);
        return response()->success(BookingResource::make($booking));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, string $id)
    {
        $data = $request->afterValidation($id);
        $booking = $this->bookingService->update(data: $data, id: $id);
        return response()->success(BookingResource::make($booking));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
