<?php

namespace App\Services;

use App\Models\Transaction;

/**
 * Class TransactionService.
 */
class TransactionService
{

    public function all($data = [], $withes = [], $paginated = false)
    {
        return Transaction::with($withes)->when(isset($data['from']), function ($query) use ($data) {
            $query->where('created_at', '>=', $data['from']);
        })->when(isset($data['to']), function ($query) use ($data) {
            $query->where('created_at', '<=', $data['to']);
        })->when(isset($data['search']), function ($query) use ($data) {
            $query->where('id', 'like', "%{$data['status']}%");
        })->when(isset($data['model_id']), function ($query) use ($data) {
            $query->where('model_id', $data['model_id']);
        })->when(isset($data['account_id']), function ($query) use ($data) {
            $query->where(function ($query) use ($data) {
                $query->where('from_account_id', $data['account_id'])
                    ->orWhere('to_account_id', $data['account_id']);
            });
        })->latest()->when($paginated, function ($query) {
            return $query->paginate(10);
        }, function ($query) {
            return $query->get();
        });
    }

    public function show($id, $withes = [])
    {
        return Transaction::with($withes)->findOrFail($id);
    }

    public function store($data)
    {
        $transaction = Transaction::create($data);
        return $transaction;
    }

    public function destroy($id)
    {
        return Transaction::whereId($id)->delete();
    }
}
