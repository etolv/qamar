<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Arr;

/**
 * Class CouponService.
 */
class CouponService
{
    public function all($search = null, $withes = [])
    {
        return Coupon::when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%$search%");
            $query->orWhere('code', 'like', "%$search%");
        })->with($withes)->latest()->get();
    }

    public function show($id)
    {
        return Coupon::withTrashed()->find($id);
    }

    public function store($data): Coupon
    {
        $coupon = Coupon::create(Arr::except($data, ['services', 'products']));
        if (isset($data['services']))
            $coupon->services()->sync($data['services']);
        if (isset($data['products']))
            $coupon->products()->sync($data['products']);
        return $coupon;
    }

    public function update($data, $id)
    {
        $coupon = Coupon::withTrashed()->find($id);
        $coupon->services()->sync($data['services'] ?? []);
        $coupon->products()->sync($data['products'] ?? []);
        return $coupon->update($data);
    }
}
