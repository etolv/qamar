<?php

namespace App\Services;

use App\Models\Address;
use Illuminate\Support\Arr;

/**
 * Class AddressService.
 */
class AddressService
{
    public function all($data = [], $paginated = false, $withes = [])
    {
        return Address::with($withes)->when(isset($data['customer_id']), function ($query) use ($data) {
            $query->where('customer_id', $data['customer_id']);
        })->when(isset($data['municipal_id']), function ($query) use ($data) {
            $query->where('municipal_id', $data['municipal_id']);
        })->when($paginated, function ($query) {
            return $query->paginate(10);
        }, function ($query) {
            return $query->get();
        });
    }

    public function show($id)
    {
        return Address::find($id);
    }

    public function store($data)
    {
        $address = Address::create(Arr::except($data, ['image']));
        if (isset($data['image'])) {
            $address->addMedia($data['image'])->toMediaCollection('image');
        }
        return $address;
    }

    public function update($data, $id)
    {
        $address = $this->show($id);
        $address->update(Arr::except($data, ['image']));
        if (isset($data['image'])) {
            $address->clearMediaCollection('image');
            $address->addMedia($data['image'])->toMediaCollection('image');
        }
        return $address;
    }

    public function destroy($id)
    {
        $address = $this->show($id);
        return $address->delete();
    }
}
