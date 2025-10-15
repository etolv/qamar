<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Models\Package;
use App\Models\Service;
use App\Models\Stock;
use App\Services\PackageService;
use Beste\Json;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PackageController extends Controller
{

    public function __construct(protected PackageService $packageService)
    {
        $this->middleware('can:read_package')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_package')->only('create', 'store');
        $this->middleware('can:update_package')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.package.index');
    }

    public function fetch()
    {
        $data = DataTables::eloquent(
            Package::withTrashed()->with([
                'items'
            ])
        )->addColumn('services_count', function ($item) {
            return $item->items->where('item_type', Service::class)->count();
        })->addColumn('products_count', function ($item) {
            return $item->items->where('item_type', Stock::class)->count();
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->addColumn('order_count', function ($item) {
            return $item->orders()->count();
        })->toJson();
        return $data;
    }

    public function search(Request $request)
    {
        $data = $this->packageService->all(search: $request->q, with: ['items'], date_from: $request->date);
        return response()->json(['data' => $data]);
    }

    public function items($package_id)
    {
        //
        $items = $this->packageService->items($package_id);
        return response()->success($items);
    }

    public function create()
    {
        return view('dashboard.package.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePackageRequest $request)
    {
        $data = $request->afterValidation();
        $package = $this->packageService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('package.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package = $this->packageService->show($id);
        return view('dashboard.package.show', compact('package'));
    }

    public function edit($id)
    {
        $package = $this->packageService->show($id);
        return view('dashboard.package.edit', compact('package'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePackageRequest $request, string $id)
    {
        $data = $request->validated();
        $package = $this->packageService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('package.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
