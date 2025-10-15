<?php

namespace App\Services;

use App\Models\Municipal;

/**
 * Class MunicipalService.
 */
class MunicipalService
{
    public function all($data = [], $paginated = false)
    {
        $query = Municipal::when(isset($data['city_id']), function ($query) use ($data) {
            $query->where('city_id', $data['city_id']);
        })->when(isset($data['search']), function ($query) use ($data) {
            $query->whereRelation('translations', 'name', 'like', "%{$data['search']}%");
        });
        if ($paginated)
            return $query->paginate();
        return $query->get();
    }

    public function show($id)
    {
        return Municipal::withTrashed()->with('city.state')->find($id);
    }

    public function store($data)
    {
        $municipal = Municipal::create($data);
        return $municipal;
    }

    public function update($data, $id)
    {
        $municipal = Municipal::find($id);
        $municipal->update($data);
        return $municipal;
    }
}
