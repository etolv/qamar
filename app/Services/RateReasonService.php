<?php

namespace App\Services;

use App\Models\RateReason;

/**
 * Class RateReasonService.
 */
class RateReasonService
{
    public function all($search = null)
    {
        return RateReason::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%");
        })->get();
    }

    public function show($id): RateReason
    {
        return RateReason::find($id);
    }

    public function store($data)
    {
        return RateReason::create($data);
    }

    public function update($data, $id)
    {
        return RateReason::find($id)->update($data);
    }
}
