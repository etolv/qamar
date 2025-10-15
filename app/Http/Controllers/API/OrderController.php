<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexOrderRequest;
use App\Http\Requests\ShowOrderRequest;
use App\Http\Requests\StoreApiOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function __construct(private OrderService $orderService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(IndexOrderRequest $request)
    {
        $data = $request->validated();
        $orders = $this->orderService->all(data: $data, paginated: true);
        $data = OrderResource::collection($orders);
        return response()->success($data, collect($data->response()->getData()->meta ?? null)->merge($data->response()->getData()->links ?? null));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreApiOrderRequest $request)
    {
        $data = $request->afterValidation();
        $order = $this->orderService->store(data: $data);
        return response()->success(OrderResource::make($order));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, ShowOrderRequest $request)
    {
        $data = $request->validated();
        $order = $this->orderService->show(id: $id, withes: ['payments', 'coupon']);
        return response()->success(OrderResource::make($order));
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
