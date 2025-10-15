<?php

namespace App\Services;

use App\Http\Controllers\front_pages\Payment;
use App\Jobs\StoreTransactionJob;
use App\Models\CashFlow;
use Carbon\Carbon;
use Illuminate\Support\Arr;

/**
 * Class CashFlowService.
 */
class CashFlowService extends BaseService
{
    public function store($data, $image = null)
    {
        $data['amount'] = $data['amount'] / $data['split_months_count'];
        if ($data['split_months_count'] > 1)
            $data['due_data'] = Carbon::now()->endOfMonth()->format('Y-m-d');
        for ($i = 0; $i < $data['split_months_count']; $i++) {
            $payment = CashFlow::create(Arr::except($data, ['split_months_count']));
            $data['due_date'] = Carbon::parse($data['due_date'])->addDay()->endOfMonth()->format('Y-m-d');
            StoreTransactionJob::dispatch($payment);
        }
        return $payment;
    }
}
