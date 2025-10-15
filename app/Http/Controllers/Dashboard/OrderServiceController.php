<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\OrderService;
use App\Http\Requests\StoreOrderServiceRequest;
use App\Http\Requests\UpdateOrderServiceEmployeeRequest;
use App\Http\Requests\UpdateOrderServiceRequest;
use App\Services\OrderServiceService;
use Illuminate\Support\Arr;

class OrderServiceController extends Controller
{

    public function __construct(protected OrderServiceService $orderServiceService)
    {
        $this->middleware('can:update_order')->only('update_employee');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function update_employee(UpdateOrderServiceEmployeeRequest $request)
    {
        $data = $request->afterValidation();
        $order_service = $this->orderServiceService->update(Arr::only($data, ['employee_id']), $data['order_service_id']);
        session()->flash('message', _t('Success'));
        return redirect()->back();
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
    public function store(StoreOrderServiceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OrderService $orderService)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrderService $orderService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderServiceRequest $request, OrderService $orderService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrderService $orderService)
    {
        //
    }
}
