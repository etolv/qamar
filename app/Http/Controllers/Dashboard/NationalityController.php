<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNationalityRequest;
use App\Models\Nationality;
use App\Services\NationalityService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NationalityController extends Controller
{

    public function __construct(protected NationalityService $nationalityService)
    {
        $this->middleware('can:read_nationality')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_nationality')->only('store', 'create');
        $this->middleware('can:update_nationality')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.nationality.index');
    }

    public function fetch()
    {
        $data = DataTables::eloquent(
            Nationality::withTrashed()->latest()
        )->addColumn('employees_count', function ($item) {
            return $item->employees()->count();
        })->toJson();
        return $data;
    }

    public function search(Request $request)
    {
        $data = $this->nationalityService->all(search: $request->q);
        return response()->json(['data' => $data]);
    }

    public function create()
    {
        return view('dashboard.nationality.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNationalityRequest $request)
    {
        $data = $request->validated();
        $nationality = $this->nationalityService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('nationality.index');
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
        $nationality = $this->nationalityService->show($id);
        return view('dashboard.nationality.edit', compact('nationality'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreNationalityRequest $request, string $id)
    {
        $data = $request->validated();
        $job = $this->nationalityService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('nationality.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
