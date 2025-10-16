<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\StoreProductController;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Catch_;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{

    public function __construct(protected CategoryService $categoryService)
    {
        $this->middleware('can:read_category')->only('index', 'fetch', 'show', 'search');
        $this->middleware('can:create_category')->only('store', 'create');
        $this->middleware('can:update_category')->only('update', 'edit');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $category = null;
        if ($request->category_id)
            $category = $this->categoryService->show($request->category_id);
        return view('dashboard.category.index', compact('category'));
    }

    public function fetch(Request $request)
    {
        $data = DataTables::eloquent(
            Category::withTrashed()->with([
                'services' => function ($query) {
                    $query->withTrashed();
                },
                'translations'
            ])->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            }, function ($query) {
                $query->where('category_id', null);
            })->latest()
        )->addColumn('image', function ($item) {
            return $item->getFirstMediaUrl('image');
        })
            ->addColumn('services_count', function ($item) {
                return $item->services()->count();
            })->toJson();
        return $data;
    }

    public function visible($id)
    {
        $this->categoryService->visible($id);
        session()->flash('message', _t('Success'));
        return redirect()->route('category.index');
    }

    public function search(Request $request)
    {
        $categories = $this->categoryService->all($request->q, ['category_id' => $request->category_id]);
        return response()->json(['data' => $categories]);
    }

    public function create()
    {
        $breadcrumbs = [
            ['link' => route('category.index'), 'name' => _t('Categories')],
            ['name' => _t('New Category')]
        ];
        return view('dashboard.category.add', compact('breadcrumbs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        $data = $request->validated();
        $category = $this->categoryService->store($data);
        session()->flash('message', _t('Success'));
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = $this->categoryService->show($id);
        if (request()->wantsJson())
            return response()->success($category);
        return true;
    }

    public function edit($id)
    {
        $category = $this->categoryService->show($id);
        return view('dashboard.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreCategoryRequest $request, $id)
    {
        $data = $request->validated();
        $category = $this->categoryService->update($data, $id);
        session()->flash('message', _t('Success'));
        return redirect()->route('category.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->categoryService->destroy($id);
        session()->flash('message', _t('Success'));
        return redirect()->route('category.index');
    }
}
