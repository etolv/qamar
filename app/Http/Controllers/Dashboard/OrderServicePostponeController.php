<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderServicePostPoneRequest;
use App\Http\Requests\StoreOrderServiceReturnRequest;
use App\Http\Requests\UpdateOrderServicePostPoneRequest;
use App\Models\OrderServicePostPone;
use App\Services\OrderServicePostPoneService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderServicePostponeController extends Controller
{

    public function __construct(protected OrderServicePostPoneService $orderServicePostponeService)
    {
        $this->middleware('can:read_order_service_postpone')->only('index', 'fetch', 'show');
        $this->middleware('can:create_order_service_postpone')->only('store', 'create');
        $this->middleware('can:update_order_service_postpone')->only('update', 'edit', 'return', 'complete');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.order.postpone.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            OrderServicePostPone::with([
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
        })->editColumn('status', function ($item) {
            return _t($item->status->name);
        })->toJson();
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderServicePostPoneRequest $request)
    {
        $data = $request->afterValidation();
        foreach ($data['order_services'] as $service)
            $postpone = $this->orderServicePostponeService->store($service);
        session()->flash('message', _t('Success'));
        return redirect()->route('order-service-postpone.index');
    }

    public function complete($id)
    {
        $postpone = $this->orderServicePostponeService->complete($id);
        session()->flash('message', _t('Success'));
        return redirect()->route('order-service-postpone.index');
    }

    public function return($id)
    {
        $postpone = $this->orderServicePostponeService->return($id);
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

    public function edit($id)
    {
        $orderServicePostpone = $this->orderServicePostponeService->show($id);
        return view('dashboard.order.postpone.edit', compact('orderServicePostpone'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderServicePostPoneRequest $request, string $id)
    {
        $data = $request->afterValidation($id);
        $postpone = $this->orderServicePostponeService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
