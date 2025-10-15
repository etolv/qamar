<?php

namespace App\Services;

use App\Enums\ServiceStatusEnum;
use App\Jobs\StoreTransactionJob;
use App\Models\OrderServiceReturn;

/**
 * Class OrderServiceReturnService.
 */
class OrderServiceReturnService
{

    public function __construct(private SettingService $settingService) {}

    public function all($data = [], $withes = [], $paginated = false)
    {
        $query = OrderServiceReturn::when(isset($data['from']), function ($query) use ($data) {
            $query->where('created_at', '>=', $data['from']);
        })->when(isset($data['to']), function ($query) use ($data) {
            $query->where('created_at', '<=', $data['to']);
        })->with($withes);
        return $paginated ? $query->paginate() : $query->get();
    }

    public function store($data)
    {
        $tax_percentage = $this->settingService->fromKey('tax')?->value ?? 15;
        $return = OrderServiceReturn::create($data);
        $order = $return->orderService->order;
        $total_return = ($return->orderService->price * $return->quantity);
        $order->update([
            'total' => $order->total - $total_return,
            'grand_total' => $order->grand_total - $total_return,
        ]);
        $return->update([
            'total' => $total_return,
            'tax' => $total_return * ($tax_percentage / 100),
            'grand_total' => $total_return + ($total_return * ($tax_percentage / 100)),
        ]);
        $return->orderService()->update(['status' => ServiceStatusEnum::RETURNED->value]);
        StoreTransactionJob::dispatch($return);
        return $return;
    }
}
