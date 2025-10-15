<?php

namespace App\Services;

use App\Models\Unit;

/**
 * Class UnitService.
 */
class UnitService
{
    public function all($search = null, $paginated = false)
    {
        $query = Unit::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%");
        })->withTrashed();
        if ($paginated)
            return $query->paginate();
        return $query->get();
    }

    public function show($id)
    {
        return Unit::withTrashed()->find($id);
    }

    public function store($data)
    {
        return Unit::create($data);
    }

    public function update($data, $id)
    {
        $unit = Unit::withTrashed()->find($id);
        $unit->update($data);
        return $unit;
    }
}
