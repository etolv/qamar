<?php

namespace App\Services;

use App\Models\Brand;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Class BrandService.
 */
class BrandService
{
    public function userBrands($user_id)
    {
        return Brand::whereHas('products', function ($query) use ($user_id) {
            $query->whereHas('user', function ($subQuery) use ($user_id) {
                $subQuery->where('id', $user_id);
            });
        });
    }
    public function all($search = null, $data = [], $perPage = null, $with = [])
    {
        return Brand::when($search, function ($query) use ($search) {
            $query->whereHas('translations', function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%$search%");
            });
        })->with($with)->paginate($perPage ?? Config::get('app.perPage'));
    }

    public function store($data)
    {
        DB::beginTransaction();
        $image = $data['image'] ?? null;
        unset($data['image']);
        $data['appear_home'] = isset($data['appear_home']);
        $category = Brand::create($data);
        if ($image) {
            $category->clearMediaCollection('image');
            $category->addMedia($image)->toMediaCollection('image');
        }
        DB::commit();
        return $category;
    }

    public function visible($id)
    {
        return Brand::where('id', $id)->update(['appear_home' => DB::raw('NOT appear_home')]);
    }

    public function show($id, $with = [], $search = null)
    {
        return Brand::withTrashed()->with($with)->find($id);
    }

    public function update($data, $id)
    {
        DB::beginTransaction();
        $image = $data['image'] ?? null;
        unset($data['image']);
        $data['appear_home'] = isset($data['appear_home']);
        $category = Brand::withTrashed()->find($id);
        $category->update($data);
        if ($image) {
            $category->clearMediaCollection('image');
            $category->addMedia($image)->toMediaCollection('image');
        }
        DB::commit();
        return $category;
    }

    public function destroy($id)
    {
        $category = Brand::find($id);
        return $category->delete();
    }
}
