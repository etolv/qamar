<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterStockWithdrawalRequest;
use App\Http\Requests\StoreStockWithdrawalRequest;
use App\Models\StockWithdrawal;
use App\Services\StockWithdrawalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpParser\Node\Expr\FuncCall;
use Yajra\DataTables\Facades\DataTables;

class StockWithdrawalController extends Controller
{
    public function __construct(protected StockWithdrawalService $stockWithdrawalService)
    {
        $this->middleware('can:read_stock_withdrawal')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_stock_withdrawal')->only('create', 'store');
        $this->middleware('can:update_stock_withdrawal')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.stock.withdrawal.index');
    }

    public function fetch(FilterStockWithdrawalRequest $request)
    {
        $filter_data = $request->afterValidation();
        $data = DataTables::eloquent(
            StockWithdrawal::with([
                'stock' => function ($query) {
                    $query->withTrashed();
                },
                'stock.product' => function ($query) {
                    $query->withTrashed();
                },
                'employee.user' => function ($query) {
                    $query->withTrashed();
                },
            ])->when($request->stock_id, function ($query) use ($request) {
                $query->where('stock_id', $request->stock_id);
            })->when($request->department, function ($query) use ($request) {
                $query->where('department', $request->department);
            })->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })->when($request->product_id, function ($query) use ($request) {
                $query->whereHas('stock', function ($query) use ($request) {
                    $query->where('product_id', $request->product_id);
                });
            })->when($request->supplier_id, function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            })->when($request->employee_id, function ($query) use ($request) {
                $query->where('employee_id', $request->employee_id);
            })->when(isset($filter_data['start']), function ($query) use ($filter_data) {
                $query->where('created_at', '>=', $filter_data['start']);
            })->when(isset($filter_data['end']), function ($query) use ($filter_data) {
                $query->where('created_at', '<=', $filter_data['end']);
            })->latest()
        )->addColumn('product_image', function ($item) {
            return $item->stock?->product?->getFirstMedia('image') ? $item->stock?->product?->getFirstMedia('image')->getUrl() : null;
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->editColumn('type', function ($item) {
            return _t($item->type?->name);
        })->addColumn('employee_image', function ($item) {
            return $item->employee?->user?->getFirstMediaUrl('profile');
        })->toJson();
        return $data;
    }

    public function create()
    {
        return view('dashboard.stock.withdrawal.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockWithdrawalRequest $request)
    {
        $data = $request->afterValidation();
        $stockWithdrawal = $this->stockWithdrawalService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('stock-withdrawal.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $withdrawal = $this->stockWithdrawalService->show($id);
        return view('dashboard.stock.withdrawal.show', compact('withdrawal'));
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
