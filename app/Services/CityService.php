<?php

namespace App\Services;

use App\Models\City;

/**
 * Class CityService.
 */
class CityService
{

    public function all($data = [], $paginated = false, $with = [])
    {
        $query = City::with($with)->when(isset($data['state_id']), function ($query) use ($data) {
            $query->where('state_id', $data['state_id']);
        })->when(isset($data['search']), function ($query) use ($data) {
            $query->whereRelation('translations', 'name', 'like', "%{$data['search']}%");
        });
        if ($paginated)
            return $query->paginate();
        return $query->get();
    }

    public function store($data)
    {
        $city = City::create($data);
        return $city;
    }

    public function update($data, $id)
    {
        $city = City::find($id);
        $city->update($data);
        return $city;
    }
}
