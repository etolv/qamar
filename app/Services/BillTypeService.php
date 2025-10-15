<?php

namespace App\Services;

use App\Models\BillType;

/**
 * Class BillTypeService.
 */
class BillTypeService
{
    public function all($search = null, $withTrashed = false)
    {
        return BillType::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%");
        })->when($withTrashed, function ($query) {
            $query->withTrashed();
        })->get();
    }

    public function show($id): BillType
    {
        return BillType::withTrashed()->find($id);
    }

    public function store($data): BillType
    {
        $bill_type = BillType::create($data);
        return $bill_type;
    }

    public function update($data, $id)
    {
        return BillType::withTrashed()->find($id)->update($data);
    }
}
