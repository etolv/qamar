<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\BookingEditRequest;
use App\Http\Requests\StoreBookingEditRequestRequest;
use App\Http\Requests\UpdateBookingEditRequestRequest;
use App\Services\BookingEditRequestService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BookingEditRequestController extends Controller
{

    public function __construct(protected BookingEditRequestService $bookingEditRequestService)
    {
        $this->middleware('can:read_booking_edit_request')->only('index', 'fetch', 'show');
        $this->middleware('can:create_booking_edit_request')->only('store', 'create');
        $this->middleware('can:update_booking_edit_request')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.booking.edit.index');
    }

    public function index_delete()
    {
        return view('dashboard.booking.edit.index-delete');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            BookingEditRequest::with([
                'booking.customer.user' => function ($query) {
                    $query->withTrashed();
                }
            ])->when(isset($request->type), function ($query) use ($request) {
                $query->where('type', $request->type);
            })->latest()
        )->addColumn('customer_image', function ($item) {
            return $item->booking->customer->user->getFirstMediaUrl('profile');
        })->editColumn('status', function ($item) {
            return _t($item->status?->name ?? '');
        })->editColumn('type', function ($item) {
            return _t($item->type?->name ?? '');
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.booking.edit.create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingEditRequestRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $booking = $this->bookingEditRequestService->show($id);
        return view('dashboard.booking.edit.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $booking = $this->bookingEditRequestService->show($id);
        return view('dashboard.booking.edit.edit', compact('booking'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingEditRequestRequest $request, $id)
    {
        $data = $request->afterValidation($id);
        $bookingEditRequest = $this->bookingEditRequestService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookingEditRequest $bookingEditRequest)
    {
        //
    }
}
