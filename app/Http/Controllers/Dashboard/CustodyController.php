<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReturnCustodyRequest;
use App\Http\Requests\StoreCustodyRequest;
use App\Http\Requests\UpdateCustodyRequest;
use App\Http\Requests\WasteCustodyRequest;
use App\Models\Custody;
use App\Models\Stock;
use App\Services\CustodyService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustodyController extends Controller
{

    public function __construct(protected CustodyService $custodyService)
    {
        $this->middleware('can:read_custody')->only('index', 'show', 'search');
        $this->middleware('can:create_custody')->only('create', 'store');
        $this->middleware('can:update_custody')->only('edit', 'update');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.custody.index');
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Custody::with([
                'employee.user' => function ($query) {
                    $query->withTrashed();
                },
                'stock.product' => function ($query) {
                    $query->withTrashed();
                },
            ])->when($request->employee_id, function ($query) use ($request) {
                $query->where('employee_id', $request->employee_id);
            })->when($request->stock_id, function ($query) use ($request) {
                $query->where('stock_id', $request->stock_id);
            })->latest()
        )->addColumn('product_image', function ($item) {
            return $item->stock->product->getFirstMediaUrl('image');
        })->addColumn('employee_image', function ($item) {
            return $item->employee?->user?->getFirstMediaUrl('profile');
        })->addColumn('status_text', function ($item) {
            return _t($item->status?->name ?? '');
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }

    public function waste(WasteCustodyRequest $request, $custody_id)
    {
        $data = $request->afterValidation($custody_id);
        $custody = $this->custodyService->waste($data, $custody_id);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    public function return(ReturnCustodyRequest $request, $custody_id)
    {
        $data = $request->validated();
        $custody = $this->custodyService->return($data, $custody_id);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    public function create()
    {
        return view('dashboard.custody.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustodyRequest $request)
    {
        $data = $request->validated();
        $custody = $this->custodyService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->route('custody.index');
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
    public function update(UpdateCustodyRequest $request, string $id)
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
