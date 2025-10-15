<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSessionRequest;
use App\Services\OrderService;
use App\Models\OrderService as OrderServiceModel;
use App\Services\OrderServiceService;
use App\Services\OrderServiceSessionService;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ListedOrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
        protected OrderServiceService $orderServiceService,
        protected OrderServiceSessionService $orderServiceSessionService,
    ) {
        $this->middleware('can:read_listed_order')->only('index', 'fetch', 'show');
        $this->middleware('can:create_listed_order')->only('store', 'create');
        $this->middleware('can:update_listed_order')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.order.listed.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            OrderServiceModel::with([
                'order.customer.user' => function ($query) {
                    $query->withTrashed();
                },
                'service.products' => function ($query) {
                    $query->withTrashed();
                },
                'sessions'
            ])->when($request->customer_id, function ($query) use ($request) {
                $query->whereHas('order', function ($subQuery) use ($request) {
                    $subQuery->where('customer_id', $request->customer_id);
                });
            })->when($request->user_branch_id, function ($query) use ($request) {
                $query->whereHas('order', function ($subQuery) use ($request) {
                    $subQuery->where('branch_id', $request->user_branch_id);
                });
            })->whereDoesntHave('return')->where('session_count', '>', '1')->latest()
        )->addColumn('customer_image', function ($item) {
            return $item->order->customer->user->getFirstMediaUrl('profile');
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->addColumn('left_sessions', function ($item) {
            return $item->session_count - $item->sessions()->count();
        })->toJson();
        return $data;
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

    public function edit($id) {}

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
