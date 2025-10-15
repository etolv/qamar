<?php

namespace App\Services;

use App\Models\BillProduct;
use Illuminate\Support\Arr;

/**
 * Class BillProductService.
 */
class BillProductService
{
    public function show($id)
    {
        return BillProduct::find($id);
    }

    public function store($data)
    {
        return BillProduct::create(Arr::only($data, [
            'product_id',
            'bill_id',
            'purchase_unit_id',
            'retail_unit_id',
            'purchase_price',
            'exchange_price',
            'sell_price',
            'quantity',
            'convert',
            'profit_percentage',
            'expiration_date',
            'barcode',
            'tax',
            'tax_type'
        ]));
    }
}
