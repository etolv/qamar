<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportProductRequest;
use App\Http\Requests\SearchProductReqeust;
use App\Http\Requests\SearchProductRequest;
use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Carbon\Carbon;
// use Barryvdh\DomPDF\Facade\Pdf;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService

    ) {
        $this->middleware('can:read_product')->only('index', 'fetch', 'show', 'search', 'barcode');
        $this->middleware('can:create_product')->only('store', 'create');
        $this->middleware('can:update_product')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.product.index');
    }

    public function create()
    {
        return view('dashboard.product.add');
    }

    public function import(ImportProductRequest $request)
    {
        $data = $request->validated();
        $services = $this->productService->import($data);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    public function search(SearchProductRequest $request)
    {
        $data = $request->validated();
        $countries = $this->productService->all($data, true);
        return response()->json(['data' => $countries]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->afterValidation();
        $product = $this->productService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('product.index', ['department' => $product->department]);
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Product::withTrashed()->with([
                'category' => function ($query) {
                    $query->withTrashed();
                },
                'brand' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->consumption_type, function ($query) use ($request) {
                $query->where('consumption_type', $request->consumption_type);
            })->when($request->department, function ($query) use ($request) {
                $query->where('department', $request->department);
            })
        )->addColumn('image', function ($item) {
            return $item->getFirstMediaUrl('image');
        })->editColumn('consumption_type', function ($item) {
            return _t($item->consumption_type->name);
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }

    public function barcode($product_id)
    {
        $product = $this->productService->show($product_id);
        // $generator = new \Picqer\Barcode\BarcodeGeneratorHTML();
        // $barcode = $generator->getBarcode('0001245786925', $generator::TYPE_CODE_128);
        $pdf = Pdf::loadView('barcode.product', [
            'product' => $product,
            // 'barcode' => $barcode,
        ]);
        return $pdf->stream("product-$product->id-" . now() . '.pdf');
        // return view('barcode.product', compact('product', 'barcode'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = $this->productService->show(id: $id, withTrashed: true);
        return view('dashboard.product.show', compact('product'));
    }

    public function edit($id)
    {
        $product = $this->productService->show(id: $id, withTrashed: true);
        return view('dashboard.product.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, string $id)
    {
        $data = $request->afterValidation();
        $product = $this->productService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('product.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
