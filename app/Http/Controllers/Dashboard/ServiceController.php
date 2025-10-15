<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportServiceRequest;
use App\Http\Requests\SearchServiceRequest;
use App\Http\Requests\StoreServiceRequest;
use App\Models\Service;
use App\Services\ServiceService;
use Illuminate\Http\Request;
use Sabberworm\CSS\Property\Import;
use Yajra\DataTables\Facades\DataTables;

class ServiceController extends Controller
{
    public function __construct(protected ServiceService $serviceService)
    {
        $this->middleware('can:read_service')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_service')->only('create', 'store');
        $this->middleware('can:update_service')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.service.index');
    }

    public function create()
    {
        return view('dashboard.service.add');
    }

    public function import(ImportServiceRequest $request)
    {
        $data = $request->validated();
        $services = $this->serviceService->import($data);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        $data = $request->afterValidation();
        $service = $this->serviceService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('service.index', ['department' => $service->department]);
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Service::withTrashed()->with([
                'category' => function ($query) {
                    $query->withTrashed();
                },
                'parent' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->department, function ($query) use ($request) {
                $query->where('department', $request->department);
            })
        )->addColumn('image', function ($item) {
            return $item->getFirstMediaUrl('image');
        })->toJson();
        return $data;
    }

    public function search(SearchServiceRequest $request)
    {
        $data = $request->validated();
        $services = $this->serviceService->all(data: $data, paginated: true);
        return response()->json(['data' => $services]);
    }

    public function products($id)
    {
        $products = $this->serviceService->products($id);
        return response()->json(['data' => $products]);
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
        $service = $this->serviceService->show($id);
        return view('dashboard.service.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreServiceRequest $request, string $id)
    {
        $data = $request->afterValidation();
        $service = $this->serviceService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('service.index', ['department' => $service->department]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
