<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBillReturnRequest;
use App\Models\BillReturn;
use App\Services\BillReturnService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use ReturnTypeWillChange;
use Yajra\DataTables\Facades\DataTables;

class BillReturnController extends Controller
{

    public function __construct(protected BillReturnService $billReturnService)
    {
        $this->middleware('can:read_bill_return')->only('index', 'fetch', 'show');
        $this->middleware('can:create_bill_return')->only('create', 'store');
        $this->middleware('can:update_bill_return')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.bill.return.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            BillReturn::with([
                'bill',
                'supplier' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->supplier_id, function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            })->when($request->bill_type_id, function ($query) use ($request) {
                $query->where('bill_id', $request->bill_type_id);
            })->latest()
        )->addColumn('supplier_image', function ($item) {
            return $item->supplier?->getFirstMedia('profile') ? $item->getFirstMedia('profile')->getUrl() : null;
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }

    public function create()
    {
        return view('dashboard.bill.return.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBillReturnRequest $request)
    {
        $data = $request->afterValidation();
        $return = $this->billReturnService->store($data);
        if (!$return) {
            session()->flash('error', _t('Requested quantity is not available'));
            return redirect()->back();
        }
        session()->flash('message', _t('Success'));
        return redirect()->route('bill-return.index');
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
