<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\FilterCustomerRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\WalletResource;
use App\Models\Customer;
use App\Models\User;
use App\Services\AuthService;
use App\Services\CityService;
use App\Services\CustomerService;
use App\Services\OrderService;
use App\Services\UserService;
use App\Services\WalletService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{

    public function __construct(
        protected CustomerService $customerService,
        // protected WalletService $walletService,
        protected AuthService $authService,
        protected UserService $userService,
        protected CityService $cityService,
        protected OrderService $orderService,
    ) {
        $this->middleware('can:read_customer')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_customer')->only('store', 'create');
        $this->middleware('can:update_customer')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.customer.index');
    }

    public function fetch(FilterCustomerRequest $request)
    {
        $filter_data = $request->afterValidation();
        $data = DataTables::eloquent(
            User::withTrashed()->with([
                'account' => function ($query) {
                    $query->withTrashed()->with([
                        'city' => function ($query) {
                            $query->withTrashed();
                        }
                    ]);
                },
            ])->when(isset($request->service_id), function ($query) use ($request) {
                $query->whereHasMorph('account', [Customer::class], function ($query) use ($request) {
                    $query->whereRelation('orders.orderServices', 'service_id', $request->service_id);
                });
            })->when(isset($filter_data['start']), function ($query) use ($filter_data) {
                $query->where('created_at', '>=', $filter_data['start']);
            })->when(isset($filter_data['end']), function ($query) use ($filter_data) {
                $query->where('created_at', '<=', $filter_data['end']);
            })->when(isset($filter_data['start_visit']) && isset($filter_data['end_visit']), function ($query) use ($filter_data) {
                $query->whereHasMorph('account', [Customer::class], function ($query) use ($filter_data) {
                    $query->whereHas('orders', function ($query) use ($filter_data) {
                        $query->whereBetween('created_at', [$filter_data['start_visit'], $filter_data['end_visit']]);
                    });
                });
            })->when(isset($filter_data['last_visit']), function ($query) use ($filter_data) {
                $query->whereHasMorph('account', [Customer::class], function ($query) use ($filter_data) {
                    $query->whereDoesntHave('orders', function ($query) use ($filter_data) {
                        $lastVisit = Carbon::now()->subDays($filter_data['last_visit'])->format('Y-m-d H:i');
                        $query->where('created_at', '>=', $lastVisit);
                    });
                });
            })->when(isset($filter_data['visit_count']), function ($query) use ($filter_data) { // visit count more than
                $query->whereHasMorph('account', [Customer::class], function ($query) use ($filter_data) {
                    $query->has('orders', '>', $filter_data['visit_count']);
                });
            })->onlyCustomers()
        )->addColumn('profile_image', function ($item) {
            return $item->getFirstMedia('profile') ? $item->getFirstMedia('profile')->getUrl() : null;
        })->addColumn('role', function ($item) {
            $role = $item->roles()->first();
            return $role?->name;
        })->editColumn('phone', function ($item) {
            return $item->dial_code . $item->phone;
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->addColumn('last_visit', function ($item) {
            $lastVisit = $item->type
                ->orders()
                ->latest()
                ->value('created_at');

            return $lastVisit ? Carbon::parse($lastVisit)->format('Y-m-d H:i') : 'No orders';
        })->addColumn('visit_count', function ($item) {
            return $item->type->orders()->count();
        })->toJson();
        return $data;
    }

    public function search(Request $request)
    {
        $customers = $this->customerService->all($request->q);
        return response()->json(['data' => $customers]);
    }

    public function orders($customer_id)
    {
        $orders = $this->orderService->all(
            [
                'customer_id' => $customer_id,
                'status' => StatusEnum::COMPLETED->value
            ],
            [
                'orderServices.service',
                'orderStocks.stock.product'
            ],
            true
        );
        return response()->json(['data' => $orders]);
    }

    public function service_and_product_history($customer_id)
    {
        $data = $this->customerService->service_and_product_history($customer_id);
        return response()->success($data);
    }

    public function points($customer_id)
    {
        $points = $this->customerService->points($customer_id);
        return response()->success($points);
    }

    public function create()
    {
        $cities = $this->cityService->all();
        return view('dashboard.customer.add', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->afterValidation();
        $user = $this->authService->register($data);
        return redirect()->route('customer.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = $this->customerService->show($id);
        // $wallet = $this->walletService->userWallet($customer->id, false);
        // $wallet = WalletResource::make(($wallet))->response()->getData();
        return view('dashboard.customer.show', compact('customer'));
    }

    public function edit($id)
    {
        $cities = $this->cityService->all();
        $customer = $this->customerService->show($id);
        return view('dashboard.customer.edit', compact('customer', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        $data = $request->validated();
        $user = $this->userService->update($data, $id);
        return redirect()->route('customer.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
