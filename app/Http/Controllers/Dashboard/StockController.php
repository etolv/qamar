<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportProductRequest;
use App\Http\Requests\SearchStockRequest;
use App\Http\Requests\StoreStockRequest;
use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{

    public function __construct(protected StockService $stockService)
    {
        $this->middleware('can:read_stock')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_stock')->only('store', 'create');
        $this->middleware('can:update_stock')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.stock.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Stock::withTrashed()->with([
                'product' => function ($query) {
                    $query->withTrashed();
                },
                'unit' => function ($query) {
                    $query->withTrashed();
                },
                'billProduct'
            ])->when($request->product_id, function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            })->when($request->department, function ($query) use ($request) {
                $query->where('department', $request->department);
            })->when($request->consumption_type, function ($query) use ($request) {
                $query->whereRelation('product', 'consumption_type', $request->consumption_type);
            })
        )->addColumn('product_image', function ($item) {
            return $item->product->getFirstMediaUrl('image');
        })->addColumn('consumption_type', function ($item) {
            return _t($item->product->consumption_type?->name);
        })->addColumn('supplier_name', function ($item) {
            return $item->billProduct?->bill?->supplier?->name;
        })->toJson();
        return $data;
    }

    public function create()
    {
        return view('dashboard.stock.add');
    }

    public function search(SearchStockRequest $request)
    {
        $data = $request->validated();
        $stocks = $this->stockService->all($data);
        return response()->json(['data' => $stocks]);
    }

    public function import(ImportProductRequest $request)
    {
        $data = $request->validated();
        $stocks = $this->stockService->import($data);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockRequest $request)
    {
        $data = $request->afterValidation();
        $stock = $this->stockService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('stock.index');
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
