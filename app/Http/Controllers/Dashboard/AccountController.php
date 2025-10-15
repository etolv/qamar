<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAccountRequest;
use App\Services\AccountService;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    public function __construct(private AccountService $accountService, private TransactionService $transactionService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $accounts = $this->accountService->all();
        return view('dashboard.account.index', compact('accounts'));
    }

    public function search(Request $request)
    {
        $accounts = $this->accountService->all(data: ['search' => $request->q], paginated: true);
        return response()->json(['data' => $accounts]);
    }

    public function transaction($account_id)
    {
        $transactions = $this->transactionService->all(data: ['account_id' => $account_id], paginated: false, withes: ['fromAccount', 'toAccount']);
        return response()->json($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountRequest $request)
    {
        $data = $request->validated();
        $account = $this->accountService->store($data);
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
