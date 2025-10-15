<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStateRequest;
use App\Models\State;
use App\Services\StateService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StateController extends Controller
{
    public function __construct(protected StateService $stateService)
    {
        $this->middleware('can:read_state')->only('index', 'fetch', 'show');
        $this->middleware('can:create_state')->only('store', 'create');
        $this->middleware('can:update_state')->only('update', 'edit');
    }

    public function index()
    {
        return view('dashboard.state.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            State::with('translations')->withTrashed()
        )->addColumn('city_count', function ($item) {
            return $item->cities()->count();
        })->addColumn('branches_count', function ($item) {
            return $item->cities()->withCount('branches')->get()->sum('branches_count');
        })->toJson();
        return $data;
    }

    public function create()
    {
        return view('dashboard.state.add');
    }

    public function store(StoreStateRequest $request)
    {
        $data = $request->validated();
        $state = $this->stateService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('state.index');
    }

    public function edit($id)
    {
        $state = State::find($id);
        return view('dashboard.state.edit', compact('state'));
    }

    public function update(StoreStateRequest $request, $id)
    {
        $data = $request->validated();
        $state = $this->stateService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('state.index');
    }
}
