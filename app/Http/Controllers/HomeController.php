<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        return view('landing.home_' . session()->get('locale', 'ar'));
    }

    public function test()
    {
        DB::enableQueryLog();
        Category::with('products')->find(1);
        Product::leftjoin('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.id', 'products.name', 'products.sku', 'categories.id')
            ->leftjoin('category_translations', 'category_translations.category_id', '=', 'categories.id')
            ->where('category_translations.locale', app()->getLocale())->get();
        dd(DB::getQueryLog());
    }

    public function terms()
    {
        return view('landing.terms_' . session()->get('locale', 'ar'));
    }

    public function privacy()
    {
        return view('landing.privacy_' . session()->get('locale', 'ar'));
    }
}
