<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Order;
use App\Models\Rate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

/**
 * Class RateService.
 */
class RateService
{
    public function fetch($request)
    {
        $data = DataTables::eloquent(
            Rate::with([
                'model.customer.user' => function ($query) {
                    $query->withTrashed();
                },
                'reason' => function ($query) {
                    $query->withTrashed();
                }
            ])->when($request->customer_id, function ($query) use ($request) {
                $query->whereHas('model', function ($subQuery) use ($request) {
                    $subQuery->where('customer_id', $request->customer_id);
                });
            })->when($request->type, function ($query) use ($request) {
                $query->where('type', $request->type);
            })->when($request->booking_id, function ($query) use ($request) {
                $query->where('model_type', Booking::class)->where('model_id', $request->booking_id);
            })->when($request->rate_reason_id, function ($query) use ($request) {
                $query->where('rate_reason_id', $request->rate_reason_id);
            })->when($request->order_id, function ($query) use ($request) {
                $query->where('model_type', Order::class)->where('model_id', $request->order_id);
            })->latest()->orderBy('model_id', 'desc')->orderBy('model_type', 'desc')
        )->addColumn('customer_image', function ($item) {
            return $item->model?->customer?->user->getFirstMediaUrl('profile');
        })->editColumn('type', function ($item) {
            return _t($item->type?->name ?? '');
        })->editColumn('created_at', function ($item) {
            return Carbon::parse($item->created_at)->format('Y-m-d H:i');
        })->editColumn('model_type', function ($item) {
            return class_basename($item->model);
        })->addColumn('action', function ($item) {
            $route = strtolower(class_basename($item->model)) . '.show';
            if (Route::has($route))
                return route($route, $item->model_id);
            else
                return '#';
        })->addColumn('model_date', function ($item) {
            return Carbon::parse($item->model->date ?? $item->model->created_at)->format('Y-m-d H:i');
        })->toJson();
        return $data;
    }
}
