<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexStockRequest;
use App\Http\Resources\StockResource;
use App\Services\StockService;
use Illuminate\Http\Request;

class StockController extends Controller
{

    public function __construct(private StockService $stockService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(IndexStockRequest $request)
    {
        $data = $request->validated();
        $stocks = $this->stockService->all(data: $data, paginated: true, withes: ['product.category', 'product.brand', 'unit']);
        $data = StockResource::collection($stocks);
        return response()->success($data, collect($data->response()->getData()->meta ?? null)->merge($data->response()->getData()->links ?? null));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stock = $this->stockService->show(id: $id, withes: ['product.category', 'product.brand', 'unit']);
        return response()->success(StockResource::make($stock));
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
