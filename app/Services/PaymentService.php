<?php

namespace App\Services;

use App\Enums\PaymentStatusEnum;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Payment;

use function PHPUnit\Framework\returnSelf;

/**
 * Class PaymentService.
 */
class PaymentService
{
    public function store($model, $data)
    {
        $payment = Payment::create([
            'model_type' => get_class($model),
            'model_id' => $model->id
        ] + $data);
        if (isset($data['file'])) {
            $payment->addMedia($data['file'])->toMediaCollection('file');
        }
        return $payment;
    }

    public function update($data, $id)
    {
        $payment = Payment::find($id);
        $payment->update($data);
        if (isset($data['file'])) {
            $payment->clearMediaCollection('file');
            $payment->addMedia($data['file'])->toMediaCollection('file');
        }
        if ($payment->model instanceof Order || $payment->model instanceof Booking) {
            if (!$payment->model->payments()->where('status', '!=', PaymentStatusEnum::PAID->value)->exists()) {
                $payment->model->update(['payment_status' => PaymentStatusEnum::PAID->value]);
            }
        }
        return $payment;
    }
}
