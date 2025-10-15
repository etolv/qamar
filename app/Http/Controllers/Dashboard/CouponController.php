<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Http\Requests\StoreCouponRequest;
use App\Http\Requests\UpdateCouponRequest;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CouponController extends Controller
{

    public function __construct(protected CouponService $couponService)
    {
        $this->middleware('can:read_coupon')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_coupon')->only('store', 'create');
        $this->middleware('can:update_coupon')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.coupon.index');
    }

    public function fetch()
    {
        $data = DataTables::eloquent(
            Coupon::withTrashed()->with([
                'services' => function ($query) {
                    $query->withTrashed();
                },
                'products' => function ($query) {
                    $query->withTrashed();
                }
            ])->latest()
        )->editColumn('is_physical', function ($item) {
            return $item->is_physical ? _t('Physical') : _t('Online');
        })->toJson();
        return $data;
    }

    public function search(Request $request)
    {
        $countries = $this->couponService->all($request->q, ['services', 'products']);
        return response()->json(['data' => $countries]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.coupon.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCouponRequest $request)
    {
        $data = $request->afterValidation();
        $coupon = $this->couponService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('coupon.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreCouponRequest $request, $id)
    {
        $data = $request->afterValidation();
        $coupon = $this->couponService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('coupon.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        //
    }
}
