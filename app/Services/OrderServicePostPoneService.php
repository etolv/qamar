<?php

namespace App\Services;

use App\Enums\ServiceStatusEnum;
use App\Models\OrderServicePostPone;

/**
 * Class OrderServicePostPoneService.
 */
class OrderServicePostPoneService
{

    public function __construct(protected OrderServiceReturnService $orderServiceReturnService) {}

    public function show($id, $withes = [])
    {
        return OrderServicePostPone::with($withes)->find($id);
    }
    public function store($data)
    {
        $postpone = OrderServicePostPone::create($data);
        $postpone->orderService()->update(['status' => ServiceStatusEnum::POSTPONED->value]);
        return $postpone;
    }

    public function update($data, $id)
    {
        $postpone = OrderServicePostPone::find($id);
        $postpone->update($data);
        return $postpone;
    }

    public function complete($id)
    {
        $postpone = OrderServicePostPone::find($id);
        $postpone->update(['status' => ServiceStatusEnum::COMPLETED->value]);
        $postpone->orderService()->update(['status' => ServiceStatusEnum::COMPLETED->value]);
        return $postpone;
    }

    public function return($id)
    {
        $postpone = OrderServicePostPone::find($id);
        $return = $this->orderServiceReturnService->store(['order_service_id' => $postpone->order_service_id]);
        $postpone->delete();
        return $postpone;
    }
}
