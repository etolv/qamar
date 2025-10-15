<?php

namespace App\Services;

use App\Models\Trip;
use PhpParser\Node\Expr\FuncCall;

/**
 * Class TripService.
 */
class TripService
{

    public function all($data = [], $paginated = false, $withes = [])
    {
        return Trip::with($withes)->when(isset($data['driver_id']), function ($query) use ($data) {
            $query->where('driver_id', $data['driver_id']);
        })->when($paginated, function ($query) {
            return $query->paginate(10);
        }, function ($query) {
            return $query->get();
        });
    }

    public function store($data)
    {
        return Trip::create($data);
    }

    public function show(string $id, $withes = []): Trip|null
    {
        return Trip::with($withes)->find($id);
    }

    public function update($data, $id)
    {
        $trip = $this->show(id: $id);
        $trip->update($data);
        return $trip;
    }
}
