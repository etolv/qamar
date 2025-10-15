<?php

namespace App\Imports;

use App\Enums\ConsumptionTypeEnum;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImport implements ToCollection
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
                if (!$row[0]) return;
                // get category or create new one
                $category = Category::whereRelation('translations', 'name', $row[0])->first();
                if (!$category) {
                    $category = Category::create([
                        'ar' => ['name' => $row[0]],
                        'en' => ['name' => $row[0]],
                    ]);
                }
                $service = Product::firstOrCreate(['name' => $row[1]], [
                    'category_id' => $category->id,
                    'sku' => $row[2],
                    'consumption_type' => ConsumptionTypeEnum::BOTH->value
                ]);
            });
        } catch (\Exception $e) {
            dd($e);
        }
        DB::commit();
    }
}
