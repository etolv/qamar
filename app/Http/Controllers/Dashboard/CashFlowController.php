<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashFlowRequest;
use App\Http\Requests\UpdateCashFlowRequest;
use App\Models\CashFlow;
use App\Services\CashFlowService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CashFlowController extends Controller
{

    public function __construct(protected CashFlowService $cashFlowService)
    {
        $this->middleware('can:read_cash_flow')->only('index', 'fetch', 'show');
        $this->middleware('can:create_cash_flow')->only('create', 'store', 'import');
        $this->middleware('can:update_cash_flow')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.cash.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            CashFlow::with([
                'flowable' => function ($query) {
                    $query->with([
                        'user' => function ($query) {
                            $query->withTrashed();
                        }
                    ]);
                }
            ])->when($request->date && str_contains($request->date, ' to '), function ($query) use ($request) {
                [$from, $to] = explode(' to ', $request->date);
                $query->whereBetween('due_date', [$from, $to]);
            })->when($request->employee_id, function ($query) use ($request) {
                $query->where('flowable_id', $request->employee_id);
            })->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })->latest()
        )->addColumn('flowable_image', function ($item) {
            return $item->flowable?->user?->getFirstMediaUrl('profile');
        })->addColumn('type_name', function ($item) {
            return _t($item->type?->name);
        })->addColumn('status_name', function ($item) {
            return _t($item->status?->name);
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }

    public function create()
    {
        return view('dashboard.cash.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCashFlowRequest $request)
    {
        $data = $request->afterValidation();
        $cash = $this->cashFlowService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('cash-flow.index');
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
    public function update(UpdateCashFlowRequest $request, string $id)
    {
        $data = $request->validated();
        $cash = $this->cashFlowService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('cash-flow.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
