<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCafeteriaOrderRequest;
use App\Models\CafeteriaOrder;
use App\Services\CafeteriaOrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CafeteriaOrderController extends Controller
{

    public function __construct(protected CafeteriaOrderService $cafeteriaOrderService)
    {
        $this->middleware('can:read_cafeteria_order')->only('index', 'fetch', 'show');
        $this->middleware('can:create_cafeteria_order')->only('store', 'create');
        $this->middleware('can:update_cafeteria_order')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.cafeteria.order.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            CafeteriaOrder::withTrashed()->with([
                'orderable',
                'branch.city' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->orderable, function ($query) use ($request) {
                $query->where('orderable', $request->orderable);
            })->when($request->user_branch_id, function ($query) use ($request) {
                $query->where('branch_id', $request->user_branch_id);
            })->when($request->date, function ($query) use ($request) {
                if ($request->date == 'today')
                    $query->where('created_at', '>=', Carbon::now()->format('Y-m-d'));
            })->latest()
        )->addColumn('type_text', function ($item) {
            return $item->type?->name;
        })->editColumn('status_text', function ($item) {
            return _t($item->status?->name ?? '');
        })->editColumn('payment_status_text', function ($item) {
            return _t($item->payment_status?->name ?? '');
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->toJson();
        // ->addColumn('services_status', function ($item) {
        //     return $item->orderServices->unique('status')->pluck('status.name')->toArray();
        //     // $has_postpone = $item->orderServices->where('status', ServiceStatusEnum::POSTPONED)->count();
        //     // $has_return = $item->orderServices->where('status', ServiceStatusEnum::RETURNED)->count();
        //     // $status = $has_postpone ? _t('Has Postpones') : '';
        //     // $status .= $has_return && $has_postpone ? " & " : '';
        //     // $status .= $has_return ? _t('Has Returns') : '';
        //     // return $status;
        // })
        return $data;
    }

    public function create()
    {
        return view('dashboard.cafeteria.order.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCafeteriaOrderRequest $request)
    {
        $data = $request->afterValidation();
        $order = $this->cafeteriaOrderService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('cafeteria-order.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = $this->cafeteriaOrderService->show($id);
        return view('dashboard.cafeteria.order.show', compact('order'));
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
