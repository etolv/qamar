<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\ModelLogEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteBillReqeust;
use App\Http\Requests\DeleteBillRequest;
use App\Http\Requests\FilterBillRequest;
use App\Http\Requests\ReceivedBillRequest;
use App\Http\Requests\SearchBillRequest;
use App\Http\Requests\StoreBillPaymentRequest;
use App\Http\Requests\StoreBillRequest;
use App\Models\Bill;
use App\Services\BillService;
use App\Services\SettingService;
use Carbon\Carbon;
use FontLib\Table\Type\cmap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

class BillController extends Controller
{

    public function __construct(protected BillService $billService, protected SettingService $settingService)
    {
        $this->middleware('can:read_bill')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:read_bill_dept')->only('debt', 'debt_fetch', 'show', 'search');
        $this->middleware('can:create_employee')->only('store', 'create');
        $this->middleware('can:update_employee')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.bill.index');
    }

    public function debt()
    {
        return view('dashboard.bill.debt');
    }

    public function debt_fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Bill::with([
                'transfers',
                'payments',
                'billType',
                'supplier' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->supplier_id, function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            })->when($request->bill_type_id, function ($query) use ($request) {
                $query->where('bill_type_id', $request->bill_type_id);
            })->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })->when($request->supplier_id, function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            })->when($request->department, function ($query) use ($request) {
                $query->where('department', $request->department);
            })->whereHas('payments', function ($query) {
                $query->select(DB::raw('SUM(amount) as total_payments'), 'model_id', 'model_type')
                    ->where('model_type', Bill::class)
                    ->groupBy('model_id', 'model_type')
                    ->havingRaw('total_payments < (SELECT total FROM bills WHERE id = payments.model_id)');
            })->latest('bills.id')
        )->addColumn('supplier_image', function ($item) {
            return $item->supplier?->getFirstMedia('profile') ? $item->getFirstMediaUrl('profile') : null;
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->editColumn('receiving_date', function ($item) {
            return Carbon::parse($item->receiving_date)->format('Y-m-d H:i');
        })->editColumn('paid', function ($item) {
            return $item->payments->sum('amount');
        })->addColumn('left', function ($item) {
            return $item->grand_total - $item->payments->sum('amount');
        })->editColumn('type', function ($item) {
            return _t($item->type?->name);
        })->addColumn('file', function ($item) {
            return $item->getFirstMedia('file') ? $item->getFirstMediaUrl('file') : null;
        })->toJson();
        return $data;
    }

    public function fetch(FilterBillRequest $request)
    {
        $filter_data = $request->afterValidation();
        $data = DataTables::eloquent(
            Bill::with([
                'transfers',
                'billType',
                'payments',
                'supplier' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->supplier_id, function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            })->when($request->bill_type_id, function ($query) use ($request) {
                $query->where('bill_type_id', $request->bill_type_id);
            })->when($request->supplier_id, function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            })->when($request->department, function ($query) use ($request) {
                $query->where('department', $request->department);
            })->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })->when(isset($filter_data['start']), function ($query) use ($filter_data) {
                $query->where('created_at', '>=', $filter_data['start']);
            })->when(isset($filter_data['end']), function ($query) use ($filter_data) {
                $query->where('created_at', '<=', $filter_data['end']);
            })->latest('bills.id')
        )->addColumn('supplier_image', function ($item) {
            return $item->supplier?->getFirstMedia('profile') ? $item->getFirstMediaUrl('profile') : null;
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->editColumn('receiving_date', function ($item) {
            return Carbon::parse($item->receiving_date)->format('Y-m-d H:i');
        })->editColumn('paid', function ($item) {
            return $item->payments->sum('amount');
        })->addColumn('left', function ($item) {
            return $item->grand_total - $item->payments->sum('amount');
        })->editColumn('type', function ($item) {
            return _t($item->type?->name);
        })->addColumn('file', function ($item) {
            return $item->getFirstMedia('file') ? $item->getFirstMediaUrl('file') : null;
        })->addColumn('user', function ($item) {
            $record = $item->modelRecord()->where('type', ModelLogEnum::CREATE->value)->first();
            if ($record) {
                return [
                    'id' => $record->user_id,
                    'name' => $record->user->name,
                    'phone' => $record->user->phone,
                    'image' => $record->user->getFirstMediaUrl('profile')
                ];
            }
            return null;
        })->toJson();
        return $data;
    }

    public function search(SearchBillRequest $request)
    {
        $data = $request->afterValidation();
        $bills = $this->billService->all(data: $data);
        return response()->json(['data' => $bills]);
    }

    public function create()
    {
        $bill_id = (Bill::latest()->first()?->id ?? 0) + 1;
        $profit_percentage = $this->settingService->fromKey('profit_percentage')?->value ?? 0;
        return view('dashboard.bill.add', compact('profit_percentage', 'bill_id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBillRequest $request)
    {
        $data = $request->afterValidation();
        $bill = $this->billService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('bill.index', ['department' => $bill->department]);
    }

    public function received(ReceivedBillRequest $request, $bill_id)
    {
        $data = $request->afterValidation($bill_id);
        $bill = $this->billService->received($bill_id);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    public function store_payment(StoreBillPaymentRequest $request, $id)
    {
        $data = $request->afterValidation($id);
        $payment = $this->billService->store_payment($id, $data);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $bill = $this->billService->show($id);
        return view('dashboard.bill.show', compact('bill'));
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
    public function destroy(DeleteBillRequest $request, $bill_id)
    {
        $data = $request->afterValidation($bill_id);
        $deleted = $this->billService->destroy($bill_id);
        if ($deleted) {
            session()->flash('message', _t('Success'));
        } else {
            session()->flash('error', _t('You can not delete this bill, Product stock has been used'));
        }
        return redirect()->back();
    }
}
