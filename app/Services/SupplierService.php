<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Supplier;

/**
 * Class SupplierService.
 */
class SupplierService
{

    public function all($data = [], $paginated = false)
    {
        $query = Supplier::when(isset($data['q']), function ($query) use ($data) {
            $query->where(function ($query) use ($data) {
                $query->where('name', 'like', "%{$data['q']}%")
                    ->orWhere('email', 'like', "%{$data['q']}%")
                    ->orWhere('phone', 'like', "%{$data['q']}%")
                    ->orWhere('address', 'like', "%{$data['q']}%");
            });
        })->when(isset($data['type']), function ($query) use ($data) {
            if (is_array($data['type'])) {
                $query->whereIn('type', $data['type']);
            } else {
                $query->where('type', $data['type']);
            }
        })->when(array_key_exists('supplier_id', $data), function ($query) use ($data) {
            $query->where('supplier_id', $data['supplier_id']);
        });
        if ($paginated) {
            return $query->paginate(10);
        }
        return $query->get();
    }

    public function show($id)
    {
        return Supplier::with('city')->find($id);
    }

    public function store($data)
    {
        $image = $data['image'] ?? null;
        unset($data['image']);
        $supplier = Supplier::create($data);
        if ($supplier->supplier) {
            $supplier->update(['company' => $supplier->supplier?->company]);
        }
        if ($image) {
            $supplier->addMedia($image)->toMediaCollection('profile');
        }
        if (isset($data['cards'])) {
            foreach ($data['cards'] as $card) {
                $supplier->cards()->create($card);
            }
        }
        return $supplier;
    }

    public function update($data, $id)
    {
        $supplier = Supplier::find($id);
        $image = $data['image'] ?? null;
        unset($data['image']);
        $supplier->update($data);
        if ($image) {
            $supplier->clearMediaCollection('profile');
            $supplier->addMedia($image)->toMediaCollection('profile');
        }
        if (isset($data['cards'])) {
            $supplier->cards()->whereNotIn('id', array_column($data['cards'], 'id'))->delete();
            foreach ($data['cards'] as $card) {
                $supplier->cards()->updateOrCreate(['id' => $card['id'] ?? null], $card);
            }
        }
        return $supplier;
    }
}
