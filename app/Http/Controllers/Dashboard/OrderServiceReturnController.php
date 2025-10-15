<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderServiceReturnRequest;
use App\Models\OrderServiceReturn;
use App\Services\OrderServiceReturnService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderServiceReturnController extends Controller
{

    public function __construct(protected OrderServiceReturnService $orderServiceReturnService)
    {
        $this->middleware('can:read_order_service_return')->only('index', 'fetch', 'show');
        $this->middleware('can:create_order_service_return')->only('store', 'create');
        $this->middleware('can:update_order_service_return')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.order.return.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            OrderServiceReturn::with([
                'orderService.order.customer.user' => function ($query) {
                    $query->withTrashed();
                },
                'orderService.service' => function ($query) {
                    $query->withTrashed();
                },
            ])->when($request->user_branch_id, function ($query) use ($request) {
                $query->whereHas('orderService.order', function ($subQuery) use ($request) {
                    $subQuery->where('branch_id', $request->user_branch_id);
                });
            })->latest()
        )->addColumn('customer_image', function ($item) {
            return $item->orderService->order->customer->user->getFirstMediaUrl('profile');
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderServiceReturnRequest $request)
    {
        $data = $request->afterValidation();
        foreach ($data['order_services'] as $service)
            $return = $this->orderServiceReturnService->store($service);
        session()->flash('message', _t('Success'));
        return redirect()->route('order-service-return.index');
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
