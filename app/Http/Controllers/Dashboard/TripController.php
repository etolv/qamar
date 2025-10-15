<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Models\Trip;
use App\Services\TripService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TripController extends Controller
{

    public function __construct(protected TripService $tripService)
    {
        $this->middleware('can:read_trip')->only('index', 'fetch', 'show');
        $this->middleware('can:create_trip')->only('store', 'create');
        $this->middleware('can:update_trip')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.trip.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Trip::with([
                'driver.user' => function ($query) {
                    $query->withTrashed();
                },
                'tripable'
            ])->when($request->driver_id, function ($query) use ($request) {
                $query->where('driver_id', $request->driver_id);
            })->latest()
        )->addColumn('driver_image', function ($item) {
            return $item->driver?->user?->getFirstMediaUrl('profile');
        })->addColumn('status_name', function ($item) {
            return $item->status?->name;
        })->addColumn('trip_type', function ($item) {
            return $item->tripable ? _t(class_basename($item->tripable)) : _t('General');
        })->toJson();
        return $data;
    }

    public function create()
    {
        return view('dashboard.trip.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTripRequest $request)
    {
        $data = $request->afterValidation();
        $trip = $this->tripService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('trip.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        $trip = $this->tripService->show($id);
        return view('dashboard.trip.edit', compact('trip'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTripRequest $request, string $id)
    {
        $data = $request->afterValidation();
        $trip = $this->tripService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('trip.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
