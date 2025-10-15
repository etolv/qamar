<?php

namespace App\Services;

use App\Models\Nationality;

/**
 * Class NationalityService.
 */
class NationalityService
{
    public function all($search = null, $paginated = false)
    {
        $query = Nationality::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('country', 'like', "%$search%");
        });
        if ($paginated) {
            return $query->paginate(10);
        }
        return $query->get();
    }

    public function show($id)
    {
        return Nationality::withTrashed()->find($id);
    }

    public function store($data)
    {
        return Nationality::create($data);
    }

    public function update($data, $id)
    {
        $nationality = Nationality::withTrashed()->find($id);
        $nationality->update($data);
        return $nationality;
    }
}
