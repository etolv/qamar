<?php

namespace App\Services;

use App\Models\Package;
use App\Models\Service;
use App\Models\Stock;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class PackageService.
 */
class PackageService
{
    public function __construct(
        private ProductService $productService,
        private ServiceService $serviceService,
        private StockService $stuckService,
    ) {}

    public function all($search = null, $paginate = true, $with = [], $date_from = null, $data = [])
    {
        $packages = Package::when($search, function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->when($date_from, function ($query) use ($date_from) {
            $query->whereDate('start_date', '<=', $date_from);
        })->when(isset($data['start_date']), function ($query) use ($data) {
            $query->whereDate('start_date', '<=', $data['start_date']);
        })->with($with);
        if ($paginate)
            return $packages->paginate();
        return $packages->get();
    }

    public function show($id, $withes = [], $withTrashed = false)
    {
        return Package::with(array_merge($withes, [
            'stockItems' => fn($query) => $query->with('item.product.stock'),
            'serviceItems' => fn($query) => $query->with('item.productServices.product')
        ]))->withTrashed()->find($id);
    }

    public function update($data, $id): Package
    {
        $package = $this->show($id);
        $package->update(Arr::except($data, ['image']));
        if (isset($data['image'])) {
            $package->clearMediaCollection('image');
            $package->addMedia($data['image'])->toMediaCollection('image');
        }
        return $package;
    }

    public function items($package_id)
    {
        return Package::with([
            'items' => function ($query) {
                $query->with([
                    'item' => function ($morphTo) {
                        $morphTo->morphWith([
                            Stock::class => ['product'],
                            Service::class => ['productServices.product']
                        ]);
                    }
                ]);
            }
        ])->find($package_id)->items;
    }

    public function store($data)
    {
        DB::beginTransaction();
        $package = Package::create(Arr::except($data, ['stocks', 'services', 'image']));
        $total = 0;
        if (isset($data['stocks'])) {
            foreach ($data['stocks'] as $stock) {
                $product_stock = $this->stuckService->show($stock['id']); //TODO check product stock in validation
                $package->items()->create([
                    'item_id' => $stock['id'],
                    'item_type' => get_class($product_stock),
                    'quantity' => $stock['quantity'],
                    'price' => $stock['price']
                ]);
                $total += ($stock['price'] * $stock['quantity']);  //TODO remove product qty from stocks
            }
        }
        if (isset($data['services'])) {
            foreach ($data['services'] as $service) {
                $service_model = $this->serviceService->show($service['id']);
                $package->items()->create([
                    'item_id' => $service['id'],
                    'item_type' => get_class($service_model),
                    'quantity' => $service['quantity'],
                    'price' => $service['price'],
                ]);
                $total += ($service['price'] * $service['quantity']);
            }
        }
        if (isset($data['image'])) {
            $package->addMedia($data['image'])->toMediaCollection('image');
        }
        $discount = 0;
        $package->update(['total' => $total]);
        DB::commit();
        return $package;
    }
}
