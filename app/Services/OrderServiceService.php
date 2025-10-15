<?php

namespace App\Services;

use App\Models\OrderService;

/**
 * Class OrderServiceService.
 */
class OrderServiceService
{
    public function show($id)
    {
        return OrderService::with([
            'order.customer.user' => function ($query) {
                $query->withTrashed();
            },
            'service' => function ($query) {
                $query->withTrashed();
            },
            'employee.user' => function ($query) {
                $query->withTrashed();
            },
            'sessions'
        ])->find($id);
    }

    public function store($data): OrderService
    {
        return OrderService::create($data);
    }

    public function update($data, $id): OrderService
    {
        $order_service = $this->show($id);
        $order_service->update($data);
        return $order_service;
    }
}
