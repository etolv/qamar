<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Class CategoryService.
 */
class CategoryService
{

    public function userCategories($user_id)
    {
        return Category::whereHas('products', function ($query) use ($user_id) {
            $query->whereHas('user', function ($subQuery) use ($user_id) {
                $subQuery->where('id', $user_id);
            });
        });
    }
    public function all($search = null, $data = [], $perPage = null, $with = [])
    {
        return Category::when($search, function ($query) use ($search) {
            $query->whereHas('translations', function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%$search%");
            });
        })->when(isset($data['category_id']), function ($query) use ($data) {
            $query->where('category_id', $data['category_id']);
        }, function ($query) {
            $query->whereNull('category_id');
        })->with($with)->paginate($perPage ?? Config::get('app.perPage'));
    }

    public function mainCategories($has_product = false)
    {
        return Category::whereNull('category_id')->when($has_product, function ($query) {
            $query->whereHas('products');
        })->get();
    }

    public function subCategories($has_product = false)
    {
        return Category::whereNotNull('category_id')->when($has_product, function ($query) {
            $query->whereHas('products');
        })->get();
    }

    public function store($data)
    {
        DB::beginTransaction();
        $category = Category::create(Arr::except($data, ['image']));
        if (isset($data['image'])) {
            $category->clearMediaCollection('image');
            $category->addMedia($data['image'])->toMediaCollection('image');
        }
        DB::commit();
        return $category;
    }

    public function visible($id)
    {
        return Category::where('id', $id)->update(['appear_home' => DB::raw('NOT appear_home')]);
    }

    public function show($id, $with = [], $search = null)
    {
        return Category::withTrashed()->with($with)->with([
            'categories' => function ($query) use ($search) {
                $query->withTrashed()->whereHas('translations', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%$search%");
                });
            },
            'parent' => function ($query) {
                $query->withTrashed();
            }
        ])->find($id);
    }

    public function update($data, $id)
    {
        DB::beginTransaction();
        $image = $data['image'] ?? null;
        unset($data['image']);
        $category = Category::find($id);
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
        $category = Category::find($id);
        return $category->delete();
    }
}
