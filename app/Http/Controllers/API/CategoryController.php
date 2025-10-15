<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function __construct(private CategoryService $categoryService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = $this->categoryService->all();
        $data = CategoryResource::collection($categories);
        return response()->success($data, collect($data->response()->getData()->meta ?? null)->merge($data->response()->getData()->links ?? null));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = $this->categoryService->show($id);
        return response()->success(CategoryResource::make($category));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
