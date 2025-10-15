<?php

namespace App\Services;

use App\Imports\ProductImport;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class ProductService.
 */
class ProductService
{
    public function all($data = [], $paginated = false, $withes = [])
    {
        $query = Product::with($withes)->when(isset($data['name']), function ($query) use ($data) {
            $query->where('name', 'like', '%' . $data['name'] . '%');
        })->when(isset($data['min_quantity']), function ($query) use ($data) {
            $query->whereHas('stocks', function ($subQuery) use ($data) {
                $subQuery->where('quantity', '>=', $data['min_quantity']);
            });
        })->when(isset($data['department']), function ($query) use ($data) {
            $query->where('department', $data['department']);
        })->when(isset($data['brand_id']), function ($query) use ($data) {
            $query->where('brand_id', $data['brand_id']);
        })->when(isset($data['category_id']), function ($query) use ($data) {
            $query->where('category_id', $data['category_id']);
        })->when(isset($data['consumption_types']), function ($query) use ($data) {
            $query->whereIn('consumption_type', $data['consumption_types']);
        })->when(isset($data['q']), function ($query) use ($data) {
            $query->where(function ($query) use ($data) {
                $query->where('name', 'like', "%{$data['q']}%")
                    ->orWhere('sku', 'like', "%{$data['q']}%");
            });
        });
        if ($paginated)
            return $query->paginate();
        return $query->get();
    }

    public function import($data)
    {
        Excel::import(new ProductImport, $data['file']);
        return true;
    }

    public function show($id, $withTrashed = false, $withes = [], $withStock = false): Product | null
    {
        $product = Product::when($withTrashed, function ($query) {
            $query->withTrashed();
        })->with(array_merge($withes, ['category', 'brand']))->find($id);
        if ($product && $withStock) {
            $product->setRelation('stock', Stock::where('product_id', $product->id)->where('quantity', '>', 0)->first());
        }
        return $product;
    }

    public function product_stock($id)
    {
        $stock = Stock::where('product_id', $id)->where('quantity', '>', 0)->first();
        return $stock;
    }

    public function store($data)
    {
        DB::beginTransaction();
        $image = $data['image'] ?? null;
        unset($data['image']);
        $product = Product::create($data);
        if ($image) {
            $product->addMedia($image)->toMediaCollection('image');
        }
        DB::commit();
        return $product;
    }

    public function update($data, $id)
    {
        DB::beginTransaction();
        $image = $data['image'] ?? null;
        unset($data['image']);
        $product = Product::withTrashed()->find($id);
        $product->update($data);
        if ($image) {
            $product->clearMediaCollection('image');
            $product->addMedia($image)->toMediaCollection('image');
        }
        DB::commit();
        return $product;
    }
}
