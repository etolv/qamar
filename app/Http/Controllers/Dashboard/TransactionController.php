<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\DestroyTransactionRequest;
use App\Http\Requests\IndexTransactionRequest;
use App\Http\Requests\StoreTransactionRequest;
use App\Models\Transaction;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{

    public function __construct(private TransactionService $transactionService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.transaction.index');
    }

    public function fetch(IndexTransactionRequest $request)
    {
        $data = $request->afterValidation();
        return DataTables::eloquent(
            Transaction::with([
                'fromAccount',
                'toAccount',
                'model'
            ])->when($request->account_id, function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('from_account_id', $request->account_id)
                        ->where('to_account_id', $request->account_id);
                });
            })->when(isset($data['from']), function ($query) use ($data) {
                $query->where('bills.created_at', '>=', $data['from']);
            })->when(isset($data['to']), function ($query) use ($data) {
                $query->where('bills.created_at', '<=', $data['to']);
            })->latest()
        )->addColumn('model_type', function ($item) {
            if ($item->model) {
                return class_basename($item->model);
            } else {
                return _t('General');
            }
        })->addColumn('from_text', function ($item) {
            return "{$item->fromAccount->name} ({$item->fromAccount->slug})";
        })->addColumn('to_text', function ($item) {
            return "{$item->toAccount->name} ({$item->toAccount->slug})";
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->toJson();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $data = $request->afterValidation();
        $transaction = $this->transactionService->store($data);
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
    public function destroy(string $id, DestroyTransactionRequest $request)
    {
        $data = $request->afterValidation($id);
        $deleted = $this->transactionService->destroy($id);
        session()->flash('message', _t("Success"));
        return redirect()->back();
    }
}
