<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Models\City;
use App\Models\State;
use App\Services\CityService;
use App\Services\StateService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CityController extends Controller
{
    public function __construct(protected CityService $cityService, protected StateService $stateService)
    {
        $this->middleware('can:read_city')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_city')->only('store', 'create');
        $this->middleware('can:update_city')->only('update', 'edit');
    }

    public function index()
    {
        return view('dashboard.city.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            City::withTrashed()->with([
                'state' => function ($query) {
                    $query->with('translations')->withTrashed();
                },
                'translations'
            ])
        )->toJson();
        return $data;
    }

    public function search(Request $request)
    {
        $cities = $this->cityService->all(data: ['name' => $request->q], with: 'state');
        return response()->json(['data' => $cities]);
    }

    public function create()
    {
        $states = $this->stateService->all();
        return view('dashboard.city.add', compact('states'));
    }

    public function store(StoreCityRequest $request)
    {
        $data = $request->validated();
        $city = $this->cityService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('city.index');
    }

    public function edit($id)
    {
        $city = City::find($id);
        $states = State::get();
        return view('dashboard.city.edit', compact('city', 'states'));
    }

    public function update(StoreCityRequest $request, $id)
    {
        $data = $request->validated();
        $city = $this->cityService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('city.index');
    }
}
