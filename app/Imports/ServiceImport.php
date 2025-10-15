<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class ServiceImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        DB::beginTransaction();
        $data = $collection->skip(1); // skip header
        $data->map(function ($row) {
            // get category or create new one
            $category = Category::whereRelation('translations', 'name', $row[3])->first();
            if (!$category) {
                $category = Category::create([
                    'ar' => ['name' => $row[3]],
                    'en' => ['name' => $row[3]],
                ]);
            }
            $service = Service::firstOrCreate(['name' => $row[0]], [
                'category_id' => $category->id,
                'description' => $row[1],
                'price' => $row[2],
                'sku' => rand(111111, 999999),
            ]);
        });
        DB::commit();
    }
}
