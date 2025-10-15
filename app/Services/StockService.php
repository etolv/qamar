<?php

namespace App\Services;

use App\Imports\ImportStock;
use App\Models\BillProduct;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class StockService.
 */
class StockService
{

    public function __construct(
        private TransferService $transferService,
        private SupplierService $supplierService,
        private BillProductService $billProductService
    ) {}
    public function all($data, $paginated = false, $withes = [])
    {
        $query = Stock::with($withes)->when(isset($data['product_id']), function ($query) use ($data) {
            $query->where('product_id', $data['product_id']);
        })->when(isset($data['name']), function ($query) use ($data) {
            $query->whereRelation('product', 'name', 'like', "%{$data['name']}%");
        })->when(isset($data['category_id']), function ($query) use ($data) {
            $query->whereRelation('product', 'category_id', $data['category_id']);
        })->when(isset($data['brand_id']), function ($query) use ($data) {
            $query->whereRelation('product', 'brand_id', $data['brand_id']);
        })->when(isset($data['min_quantity']), function ($query) use ($data) {
            $query->where('quantity', '>=', $data['min_quantity']);
        })->when(isset($data['excluded_ids']), function ($query) use ($data) {
            $query->whereNotIn('id', $data['excluded_ids']);
        })->when(isset($data['department']), function ($query) use ($data) {
            $query->where('department', $data['department']);
        })->when(isset($data['consumption_types']), function ($query) use ($data) {
            $query->whereHas('product', function ($q) use ($data) {
                $q->whereIn('consumption_type', $data['consumption_types']);
            });
        })->when(isset($data['bill_id']), function ($query) use ($data) {
            $query->whereRelation('billProduct', 'bill_id', $data['bill_id']);
        })->with(array_merge([
            'product',
            'unit',
            'transfersTo' => function ($query) {
                $query->where('from_type', BillProduct::class)->with('from');
            }
        ]));
        if ($paginated)
            return $query->paginate();
        return $query->get();
    }

    public function import($data)
    {
        Excel::import(new ImportStock, $data['file']);
        return true;
    }

    public function show($id, $withes = [])
    {
        return Stock::with(array_merge($withes, ['product']))->find($id);
    }

    public function product_stock($product_id, $onlySigned = true)
    {
        return Stock::where('product_id', $product_id)->when($onlySigned, function ($query) {
            $query->where('quantity', '>', 0);
        })->latest()->first();
    }

    public function withdraw($stock_id, $quantity = 1)
    {
        $stock = $this->show($stock_id);
        $stock->update(['quantity' => $stock->quantity - $quantity]);
        // if ($stock->quantity == 0) {
        //     $stock->delete();
        // }
        return $stock;
    }

    public function insert($stock_id, $quantity = 1)
    {
        $stock = $this->show($stock_id);
        $stock->update(['quantity' => $stock->quantity + $quantity]);
        // if ($stock->quantity == 0) {
        //     $stock->delete();
        // }
        return $stock;
    }

    public function store($data)
    {
        DB::beginTransaction();
        $stock = Stock::create(Arr::only($data, [
            'product_id',
            'unit_id',
            'expiration_date',
            'barcode',
            'quantity',
            'price',
            'exchange_price',
            'department'
        ]));
        DB::commit();
        return $stock;
    }
}
