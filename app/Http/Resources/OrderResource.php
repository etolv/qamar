<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'total' => $this->total,
            'tax' => $this->tax,
            'grand_total' => $this->grand_total,
            'discount' => $this->discount,
            'status' => $this->status?->name,
            'payment_type' => $this->payment_type?->name,
            'payment_status' => $this->payment_status?->name,
            'is_gift' => $this->is_gift,
            'employee' => EmployeeResource::make($this->whenLoaded('employee')),
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'coupon' => CouponResource::make($this->whenLoaded('coupon')),
            'orderStocks' => OrderStockResource::collection($this->whenLoaded('orderStocks')),
            'orderServices' => OrderServiceResource::collection($this->whenLoaded('orderServices')),
            'packages' => PackageResource::collection($this->whenLoaded('packages')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}
