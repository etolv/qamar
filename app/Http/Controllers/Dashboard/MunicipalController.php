<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMunicipalRequest;
use App\Http\Requests\UpdateMunicipalRequest;
use App\Models\Municipal;
use App\Services\MunicipalService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MunicipalController extends Controller
{

    public function __construct(protected MunicipalService $municipalService)
    {
        $this->middleware('can:read_municipal')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_municipal')->only('store', 'create');
        $this->middleware('can:update_municipal')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.municipal.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Municipal::withTrashed()->with([
                'city.state' => function ($query) {
                    $query->withTrashed();
                },
            ])
        )->toJson();
        return $data;
    }

    public function search(Request $request)
    {
        $countries = $this->municipalService->all(['name' => $request->q]);
        return response()->json(['data' => $countries]);
    }

    public function create()
    {
        return view('dashboard.municipal.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMunicipalRequest $request)
    {
        $data = $request->validated();
        $municipal = $this->municipalService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('municipal.index');
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
        $municipal = $this->municipalService->show($id);
        return view('dashboard.municipal.edit', compact('municipal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMunicipalRequest $request, string $id)
    {
        $data = $request->validated();
        $municipal = $this->municipalService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('municipal.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
