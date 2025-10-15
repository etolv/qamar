<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTransferRequest;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Transfer;
use App\Services\TransferService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Svg\Gradient\Stop;
use Yajra\DataTables\Facades\DataTables;

class TransferController extends Controller
{
    public function __construct(protected TransferService $transferService)
    {
        $this->middleware('can:read_transfer')->only('index', 'fetch', 'show');
        $this->middleware('can:create_transfer')->only('create', 'store', 'import');
        $this->middleware('can:update_transfer')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.transfer.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Transfer::with([
                'from' => function ($query) {
                    $query->withTrashed()
                        ->with([
                            'product' => function ($query) {
                                $query->withTrashed();
                            }
                        ]);
                },
                'to' => function ($query) {
                    $query->withTrashed()
                        ->with([
                            'product' => function ($query) {
                                $query->withTrashed();
                            }
                        ]);
                }
            ])->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })->latest()
        )->editColumn('from_type', function ($item) {
            return class_basename($item->from);
        })->editColumn('to_type', function ($item) {
            return class_basename($item->to);
        })->addColumn('product_image', function ($item) {
            if ($item->from_type == Stock::class) {
                return $item->from->product->getFirstMediaUrl('image');
            } else if ($item->from_type == Product::class) {
                return $item->from->getFirstMediaUrl('image');
            } else if ($item->to_type == Stock::class) {
                return $item->to->product->getFirstMediaUrl('image');
            } else if ($item->to_type == Product::class) {
                return $item->to->getFirstMediaUrl('image');
            }
        })->addColumn('product', function ($item) {
            if ($item->from_type == Stock::class) {
                return $item->from->product;
            } else if ($item->from_type == Product::class) {
                return $item->from;
            } else if ($item->to_type == Stock::class) {
                return $item->to->product;
            } else if ($item->to_type == Product::class) {
                return $item->to;
            }
        })->addColumn('unit_name', function ($item) {
            if ($item->from_type == Stock::class) {
                return $item->from->unit?->name;
            } else if ($item->from_type == Product::class) {
                return $item->from->stocks?->first()?->unit?->name;
            } else if ($item->to_type == Stock::class) {
                return $item->to->unit?->name;
            } else if ($item->to_type == Product::class) {
                return $item->to->stocks?->first()?->unit?->name;
            }
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->editColumn('type', function ($item) {
            return _t($item->type->name);
        })->toJson();
        return $data;
    }

    public function create()
    {
        return view('dashboard.transfer.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransferRequest $request)
    {
        //
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
