<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateLoyaltyRequest;
use App\Models\Loyalty;
use App\Services\LoyaltyService;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as FacadesRoute;
use Yajra\DataTables\Facades\DataTables;

class LoyaltyController extends Controller
{
    public function __construct(protected SettingService $settingService, protected LoyaltyService $loyaltyService)
    {
        $this->middleware('can:read_loyalty')->only('index', 'fetch', 'show', 'settings');
        $this->middleware('can:create_loyalty')->only('create', 'store', 'import');
        $this->middleware('can:update_loyalty')->only('edit', 'update', 'update_settings');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.loyalty.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Loyalty::withTrashed()->with([
                'customer.user' => function ($query) {
                    $query->withTrashed();
                },
                'model'
            ])->when($request->customer_id, function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            })->when($request->date, function ($query) use ($request) {
                if ($request->date == 'today')
                    $query->where('created_at', '>=', Carbon::now()->format('Y-m-d'));
            })->latest()
        )->addColumn('customer_image', function ($item) {
            return $item->customer->user->getFirstMediaUrl('profile');
        })->editColumn('model_type', function ($item) {
            return class_basename($item->model);
        })->addColumn('action', function ($item) {
            $route = strtolower(class_basename($item->model)) . '.show';
            if (FacadesRoute::has($route))
                return route($route, $item->model_id);
            else
                return '#';
        })->addColumn('model_date', function ($item) {
            return Carbon::parse($item->model->date ?? $item->model->created_at)->format('Y-m-d H:i');
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }

    public function settings()
    {
        $settings = $this->settingService->all(['cash_to_points', 'points_to_cash']);
        return view('dashboard.loyalty.setting', compact('settings'));
    }

    public function update_settings(UpdateLoyaltyRequest $request)
    {
        $data = $request->afterValidation();
        $settings = $this->loyaltyService->update_setting($data);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
