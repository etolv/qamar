<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ModelRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

class ModelRecordController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:read_model_record')->only('index', 'fetch', 'show');
        $this->middleware('can:create_model_record')->only('store', 'create');
        $this->middleware('can:update_model_record')->only('update', 'edit');
    }
    public function index()
    {
        return view('dashboard.record.index');
    }

    public function fetch()
    {
        $data = DataTables::eloquent(
            ModelRecord::with([
                'user' => function ($query) {
                    $query->withTrashed();
                },
            ])->latest()
        )->editColumn('type', function ($item) {
            return _t($item->type->name);
        })->addColumn('profile_image', function ($item) {
            return $item->user?->getFirstMedia('profile') ? $item->user?->getFirstMedia('profile')->getUrl() : null;
        })->editColumn('model_type', function ($item) {
            return class_basename($item->model);
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->addColumn('action', function ($item) {
            $route = strtolower(class_basename($item->model)) . '.edit';
            if (Route::has($route))
                return route($route, $item->model_id);
            else
                return '#';
        })->addColumn('model_name', function ($item) {
            return $item->model->name ?? $item->model->title;
        })->toJson();
        return $data;
    }
}
