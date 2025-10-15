<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\SupplierTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchSupplierReqeust;
use App\Http\Requests\SearchSupplierRequest;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use App\Services\CityService;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{

    public function __construct(protected SupplierService $supplierService, protected CityService $cityService)
    {
        $this->middleware('can:read_supplier')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_supplier')->only('store', 'create');
        $this->middleware('can:update_supplier')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $supplier = null;
        if ($request->supplier_id) {
            $supplier = $this->supplierService->show($request->supplier_id);
        }
        return view('dashboard.supplier.index', compact('supplier'));
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Supplier::withTrashed()->with([
                'city' => function ($query) {
                    $query->withTrashed();
                },
                'supplier' => function ($query) {
                    $query->withTrashed();
                },
            ])->when($request->supplier_id, function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            }, function ($query) {
                $query->whereNull('supplier_id');
            })->latest()
        )->addColumn('profile_image', function ($item) {
            return $item->getFirstMedia('profile') ? $item->getFirstMediaUrl('profile') : null;
        })->addColumn('supplier_image', function ($item) {
            return $item->supplier?->getFirstMedia('profile') ? $item->getFirstMediaUrl('profile') : null;
        })->editColumn('type', function ($item) {
            return _t($item->type?->name);
        })->toJson();
        return $data;
    }

    public function search(SearchSupplierRequest $request)
    {
        $data = $request->afterValidation();
        $suppliers = $this->supplierService->all($data);
        return response()->json(['data' => $suppliers]);
    }

    public function create(Request $request)
    {
        $types = SupplierTypeEnum::cases();
        $supplier = $this->supplierService->show($request->supplier_id);
        $cities = $this->cityService->all();
        return view('dashboard.supplier.add', compact('cities', 'supplier', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierRequest $request)
    {
        $data = $request->validated();
        $supplier = $this->supplierService->store($data);
        session()->flash('message', _t('Success'));
        if ($supplier->supplier_id) {
            return redirect()->route('supplier.index', ['supplier_id' => $supplier->supplier_id]);
        }
        return redirect()->route('supplier.index');
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
        $types = SupplierTypeEnum::cases();
        $cities = $this->cityService->all();
        $supplier = $this->supplierService->show($id);
        return view('dashboard.supplier.edit', compact('cities', 'supplier', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, string $id)
    {
        $data = $request->validated();
        $supplier = $this->supplierService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('supplier.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
