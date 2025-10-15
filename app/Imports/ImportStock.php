<?php

namespace App\Imports;

use App\Enums\BillTypeEnum;
use App\Enums\ConsumptionTypeEnum;
use App\Enums\DepartmentEnum;
use App\Enums\PaymentTypeEnum;
use App\Enums\TaxTypeEnum;
use App\Models\Bill;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Unit;
use App\Services\BillService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportStock implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        DB::beginTransaction();
        try {
            $data = $collection->skip(1); // skip header
            $data->map(function ($row) use (&$data_row) {
                if (!$row[0] || !$row[5] || !$row[4]) return;
                $product = Product::where('name', 'like', $row[1])->first();
                $consumption_type = ConsumptionTypeEnum::fromName($row[9])?->value;
                $brand = Brand::whereRelation('translations', 'name', $row[8])->first();
                if ($row[8] && !$brand) {
                    $brand = Brand::create([
                        'ar' => ['name' => $row[8]],
                        'en' => ['name' => $row[8]],
                    ]);
                }
                $category = Category::whereRelation('translations', 'name', $row[0])->first();
                if (!$category) {
                    $category = Category::create([
                        'ar' => ['name' => $row[0]],
                        'en' => ['name' => $row[0]],
                    ]);
                }
                if (!$product) {
                    $product = Product::create([
                        'name' => $row[1],
                        'sku' => $row[2],
                        'category_id' => $category->id,
                        'brand_id' => $brand?->id,
                        'consumption_type' => $consumption_type
                    ]);
                }
                $sell_price = $row[4] + $row[4] * (10 / 100);
                // $min_price = $row[4] + $row[4] * (5 / 100);
                $expiration_date = null;
                if ($row[6]) {
                    $daysSinceExcelStart = 46025;
                    $baseDate = Carbon::create(1899, 12, 30);
                    $expiration_date = $baseDate->addDays($daysSinceExcelStart)->format('Y-m-d');
                }
                $bill_id = rand(1, 99999999);
                $supplier = Supplier::firstOrCreate(['name' => $row[7]], ['city_id' => City::first()?->id]);
                $identifier = 'B' . $bill_id  . $supplier->id . Carbon::now()->format('ymd');
                $bill = [
                    'identifier' => $identifier,
                    'supplier_id' => $supplier->id,
                    'tax_type' => TaxTypeEnum::TAXED->value,
                    'department' => DepartmentEnum::SALON->value,
                    'payment_type' => PaymentTypeEnum::CASH->value,
                    'type' => BillTypeEnum::PURCHASE->value,
                    'received' => true,
                    'paid' => 0,
                    'receiving_date' => Carbon::now()->format('Y-m-d'),
                    'products' => [
                        [
                            'product_id' => $product->id,
                            'quantity' => $row[5] ?? 1,
                            'purchase_price' => $row[4],
                            'sell_price' => $sell_price,
                            // 'min_price' => $min_price,
                            'exchange_price' => $row[4],
                            'convert' => 1,
                            'expiration_date' => $expiration_date,
                            'retail_unit_id' => Unit::find(4)->id,
                            'purchase_unit_id' => Unit::find(4)->id,
                            'tax_type' => TaxTypeEnum::TAXED->value,
                        ]
                    ]
                ];
                $bill_model = resolve(BillService::class)->store($bill);
            });
        } catch (\Exception $e) {
            dd($e);
        }
        DB::commit();
    }
}
