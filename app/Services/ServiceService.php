<?php

namespace App\Services;

use App\Imports\ServiceImport;
use App\Models\Service;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class ServiceService.
 */
class ServiceService
{
    public function all($data = [], $paginated = false)
    {
        return Service::with('products')->when(isset($data['name']), function ($query) use ($data) {
            $query->where('name', 'like', "%{$data['name']}%");
        })->when(isset($data['q']), function ($query) use ($data) {
            $query->where('name', 'like', "%{$data['q']}%");
        })->when(isset($data['department']), function ($query) use ($data) {
            $query->where('department', $data['department']);
        })->when(isset($data['category_id']), function ($query) use ($data) {
            $query->where('category_id', $data['category_id']);
        })->when($paginated, function ($query) {
            return $query->paginate();
        }, function ($query) {
            return $query->get();
        });
    }

    public function import($data)
    {
        Excel::import(new ServiceImport, $data['file']);
        return true;
    }

    public function products($id)
    {
        return Service::withTrashed()->with('productServices.product')->find($id);
    }

    public function show($id, $withTrashed = true, $withes = [])
    {
        return Service::withTrashed($withTrashed)
            ->with(array_merge($withes, ['category', 'parent', 'productServices.product']))->find($id);
    }

    public function store($data)
    {
        DB::beginTransaction();
        $data['sku'] = $this->generateSku("SER");
        $service = Service::create(Arr::except($data, ['products', 'image', 'sub_categories', 'terms']));
        if (isset($data['image'])) {
            $service->addMedia($data['image'])->toMediaCollection('image');
        }
        if (isset($data['terms'])) {
            $service->clearMediaCollection('terms');
            $service->addMedia($data['terms'])->toMediaCollection('terms');
        }
        if (isset($data['products'])) {
            foreach ($data['products'] as $product) {
                $service->productServices()->create([
                    'product_id' => $product['id'],
                    'required' => isset($product['required'])
                ]);
            }
        }
        if (isset($data['sub_categories'])) {
            $service->categories()->attach($data['sub_categories']);
        }
        DB::commit();
        return $service;
    }

    public function generateSku($prefix)
    {
        $sku = uniqid($prefix);
        while (Service::whereSku($sku)->exists()) {
            $sku = uniqid($prefix);
        }
        return $sku;
    }

    public function update($data, $id)
    {
        DB::beginTransaction();
        $service = Service::withTrashed()->find($id);
        $service->update(Arr::except($data, ['products', 'image', 'sub_categories', 'terms']));
        if (isset($data['image'])) {
            $service->clearMediaCollection('image');
            $service->addMedia($data['image'])->toMediaCollection('image');
        }
        if (isset($data['terms'])) {
            $service->clearMediaCollection('terms');
            $service->addMedia($data['terms'])->toMediaCollection('terms');
        }
        if (isset($data['products'])) {
            $service->product_service()->delete();
            foreach ($data['products'] as $product) {
                $service->productServices()->create([
                    'product_id' => $product['id'],
                    'required' => isset($product['required'])
                ]);
            }
        }
        if (isset($data['sub_categories'])) {
            $service->categories()->detach();
            $service->categories()->attach($data['sub_categories']);
        }
        DB::commit();
        return $service;
    }
}
