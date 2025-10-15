<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\StoreBrandRequest;
use App\Models\Branch;
use App\Services\BranchService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BranchController extends Controller
{

    public function __construct(protected BranchService $branchService)
    {
        $this->middleware('can:read_branch')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_branch')->only('store', 'create');
        $this->middleware('can:update_branch')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.branch.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Branch::withTrashed()->with([
                'city' => function ($query) {
                    $query->withTrashed();
                },
            ])->when($request->state_id, function ($query) use ($request) {
                $query->whereRelation('city', 'state_id', $request->state_id);
            })->latest()
        )->editColumn('is_physical', function ($item) {
            return $item->is_physical ? _t('Physical') : _t('Online');
        })->toJson();
        return $data;
    }

    public function create()
    {
        return view('dashboard.branch.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBranchRequest $request)
    {
        $data = $request->afterValidation();
        $branch = $this->branchService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('branch.index');
    }

    public function search(Request $request)
    {
        $branches = $this->branchService->all(['name' => $request->q]);
        return response()->json(['data' => $branches]);
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
        $branch = $this->branchService->show($id);
        return view('dashboard.branch.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBranchRequest $request, string $id)
    {
        $data = $request->afterValidation();
        $branch = $this->branchService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('branch.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
