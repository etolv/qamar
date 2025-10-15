<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Brand;

/**
 * Class BranchService.
 */
class BranchService
{
    public function all($data = [])
    {
        return Branch::with([
            'city' => function ($query) {
                $query->withTrashed();
            },
        ])->when(isset($data['name']), function ($query) use ($data) {
            $query->where('name', 'like', '%' . $data['name'] . '%')
                ->orWhere('address', 'like', '%' . $data['name'] . '%');
        })->latest()->get();
    }

    public function show($id)
    {
        return Branch::with([
            'city' => function ($query) {
                $query->withTrashed();
            },
        ])->find($id);
    }

    public function store($data)
    {
        return Branch::create($data);
    }

    public function update($data, $id)
    {
        $branch = Branch::find($id);
        $branch->update($data);
        return $branch;
    }
}
