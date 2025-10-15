<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Services\BookingService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use SebastianBergmann\Invoker\TimeoutException;
use Yajra\DataTables\Facades\DataTables;

class BookingController extends Controller
{

    public function __construct(protected BookingService $bookingService)
    {
        $this->middleware('can:read_booking')->only('index', 'fetch', 'show', 'today', 'pending', 'search');
        $this->middleware('can:create_booking')->only('store', 'create');
        $this->middleware('can:update_booking')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.booking.index');
    }

    public function search(Request $request)
    {
        $data = $this->bookingService->all(data: ['search' => $request->q], withes: ['customer.user'], paginated: true);
        return response()->json(['data' => $data]);
    }

    public function today()
    {
        $date = Carbon::now()->format('Y-m-d');
        return view('dashboard.booking.index', compact('date'));
    }

    public function pending()
    {
        $status = StatusEnum::PENDING->value;
        return view('dashboard.booking.index', compact('status'));
    }

    public function fetch(Request $request)
    {
        $data = $this->bookingService->fetch($request);
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.booking.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $data = $request->afterValidation();
        $booking = $this->bookingService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('booking.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $booking = $this->bookingService->show($id);
        return view('dashboard.booking.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        return view('dashboard.booking.edit', compact('booking'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, $id)
    {
        $data = $request->afterValidation($id);
        $booking = $this->bookingService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('booking.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //
    }
}
