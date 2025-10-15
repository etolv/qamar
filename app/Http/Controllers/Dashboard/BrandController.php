<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    public function __construct(protected BrandService $brandService)
    {
        $this->middleware('can:read_brand')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_brand')->only('store', 'create');
        $this->middleware('can:update_brand')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.brand.index');
    }

    public function fetch()
    {
        $data = DataTables::eloquent(
            Brand::withTrashed()->with([
                'products' => function ($query) {
                    $query->withTrashed();
                },
                'translations'
            ])->latest()
        )->addColumn('image', function ($item) {
            return $item->getFirstMediaUrl('image');
        })->addColumn('products_count', function ($item) {
            return $item->products()->count();
        })->toJson();
        return $data;
    }

    public function search(Request $request)
    {
        $categories = $this->brandService->all($request->q, []);
        return response()->json(['data' => $categories]);
    }

    public function create()
    {
        return view('dashboard.brand.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request)
    {
        $data = $request->validated();
        $brand = $this->brandService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('brand.index');
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
        $brand = $this->brandService->show($id);
        return view('dashboard.brand.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBrandRequest $request, string $id)
    {
        $data = $request->validated();
        $brand = $this->brandService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('brand.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
