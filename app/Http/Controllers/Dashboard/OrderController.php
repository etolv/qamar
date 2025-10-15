<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ServiceStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteOrderRequest;
use App\Http\Requests\StoreOrderRateRequest;
use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Services\CustomerService;
use App\Services\OrderService;
use App\Services\SettingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
// use Spatie\LaravelPdf\Facades\Pdf;
// use Barryvdh\DomPDF\Facade\Pdf;
// use PDF;
use niklasravnsborg\LaravelPdf\Facades\Pdf as PDF;
use Illuminate\Support\Facades\App;
use Yajra\DataTables\Facades\DataTables;


class OrderController extends Controller
{

    public function __construct(protected OrderService $orderService, protected SettingService $settingService, protected CustomerService $customerService)
    {
        $this->middleware('can:read_order')->only('index', 'fetch', 'show');
        $this->middleware('can:create_order')->only('store', 'create');
        $this->middleware('can:update_order')->only('update', 'edit', 'complete');
        $this->middleware('can:export_order')->only('pdf');
        $this->middleware('can:read_rate')->only('rate');
        $this->middleware('can:create_rate')->only('submit_rate');
        $this->middleware('can:create_order_service_postpone')->only('postpone');
        $this->middleware('can:create_order_service_return')->only('return');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customer_order_count = $this->orderService->customerOrderCount();
        return view('dashboard.order.index', compact('customer_order_count'));
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Order::withTrashed()->with([
                'customer.user' => function ($query) {
                    $query->withTrashed();
                },
                'employee.user' => function ($query) {
                    $query->withTrashed();
                },
                'branch.city' => function ($query) {
                    $query->withTrashed();
                },
                'gifter.user' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->customer_id, function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            })->when($request->user_branch_id, function ($query) use ($request) {
                $query->where('branch_id', $request->user_branch_id);
            })->when($request->department, function ($query) use ($request) {
                $query->where('department', $request->department);
            })->when($request->is_mobile, function ($query) use ($request) {
                $query->where('is_mobile', $request->is_mobile);
            })->when($request->date, function ($query) use ($request) {
                if ($request->date == 'today')
                    $query->where('created_at', '>=', Carbon::now()->format('Y-m-d'));
            })->latest()
        )->addColumn('customer_image', function ($item) {
            return $item->customer->user->getFirstMediaUrl('profile');
        })->addColumn('employee_image', function ($item) {
            return $item->employee?->user?->getFirstMediaUrl('profile');
        })->addColumn('gifter_image', function ($item) {
            return $item->gifter?->user->getFirstMediaUrl('profile');
        })->addColumn('average_rate', function ($item) {
            return $item->rates()->average('rate') ?? _t('Not Rated');
        })->editColumn('status_name', function ($item) {
            return _t($item->status?->name ?? '');
        })->editColumn('payment_status', function ($item) {
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

    public function search(Request $request)
    {
        $orders = $this->orderService->all(data: ['search' => $request->q], paginated: true, withes: ['customer.user']);
        return response()->json(['data' => $orders]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $point_to_cash = $this->settingService->fromKey('points_to_cash')?->value ?? 0;
        $customers = $this->customerService->all();
        return view('dashboard.order.add', compact('customers', 'point_to_cash'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $data = $request->afterValidation();
        $order = $this->orderService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('order.show', $order->id);
    }

    public function pdf($id)
    {
        $order = $this->orderService->show($id);
        $font_family = "amiri";
        $direction = 'ltr';
        $text_align = 'left';
        $not_text_align = 'right';
        $config = [];
        if (session('locale') == 'ar') {
            $direction = 'rtl';
            $text_align = 'right';
            $not_text_align = 'left';
        }
        $settings = $this->settingService->all();
        $pageConfigs = ['myLayout' => 'blank'];
        // pdf.invoice
        return view('pdf.invoice', [
            'order' => $order,
            'font_family' => 'amiri',
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align,
            'settings' => $settings,
            'pageConfigs' => $pageConfigs
        ]);
        $pdf = PDF::loadView('pdf.invoice', [
            'order' => $order,
            'font_family' => 'amiri',
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align,
            'settings' => $settings,
            'pageConfigs' => $pageConfigs
        ]);
        return $pdf->stream("order-$order->id-" . now() . '.pdf');
    }

    public function postpone($id)
    {
        $order = $this->orderService->show($id);
        return view('dashboard.order.postpone', compact('order'));
    }

    public function return($id)
    {
        $order = $this->orderService->show($id);
        return view('dashboard.order.return', compact('order'));
    }

    public function rate($id)
    {
        $order = $this->orderService->show($id);
        return view('dashboard.order.rate', compact('order'));
    }

    public function submit_rate(StoreOrderRateRequest $request, $id)
    {
        $data = $request->afterValidation($id);
        $order = $this->orderService->rate($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('order.show', $order->id);
    }

    public function complete(CompleteOrderRequest $request, $id)
    {
        $data = $request->afterValidation($id);
        $order = $this->orderService->complete($id);
        session()->flash('message', _t('Success'));
        return redirect()->route('order.show', $order->id);
    }

    public function submit_return(Request $request, $id)
    {
        //
    }
    public function submit_postpone(Request $request, $id)
    {
        //
    }

    public function test_pdf($id)
    {
        $order = $this->orderService->show($id);
        $font_family = "cairo";
        $direction = 'ltr';
        $text_align = 'left';
        $not_text_align = 'right';
        $settings = $this->settingService->all();
        if (session('locale') == 'ar') {
            $font_family = "'cairo'";
            $direction = 'rtl';
            $text_align = 'right';
            $not_text_align = 'left';
        }
        return view('pdf.invoice', [
            'order' => $order,
            'font_family' => $font_family,
            'direction' => $direction,
            'text_align' => $text_align,
            'not_text_align' => $not_text_align,
            'settings' => $settings,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = $this->orderService->show($id);
        return view('dashboard.order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $order = $this->orderService->show($id);
        return view('dashboard.order.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, $id)
    {
        $data = $request->afterValidation($id);
        $order = $this->orderService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('order.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
