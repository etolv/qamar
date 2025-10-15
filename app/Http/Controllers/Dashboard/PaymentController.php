<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

class PaymentController extends Controller
{

    public function __construct(protected PaymentService $paymentService)
    {
        $this->middleware('can:read_payment')->only('index', 'fetch', 'show');
        $this->middleware('can:create_payment')->only('store', 'create');
        $this->middleware('can:update_payment')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.payment.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Payment::with([
                'model',
                'card' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->order_id, function ($query) use ($request) {
                $query->where('model_type', 'App\Models\Order')->where('model_id', $request->order_id);
            })->when($request->booking, function ($query) use ($request) {
                $query->where('model_type', 'App\Models\Booking')->where('model_id', $request->booking);
            })->latest()
        )->editColumn('type', function ($item) {
            return _t($item->type?->name ?? '');
        })->editColumn('status', function ($item) {
            return _t($item->status?->name ?? '');
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->editColumn('model_type', function ($item) {
            return class_basename($item->model);
        })->addColumn('action', function ($item) {
            $route = strtolower(class_basename($item->model)) . '.show';
            if (Route::has($route))
                return route($route, $item->model_id);
            else
                return '#';
        })->addColumn('model_date', function ($item) {
            return Carbon::parse($item->model?->date ?? $item->model?->created_at)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request)
    {
        $data = $this->afterValidation();
        // $payment = $this->paymentService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->back();
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
