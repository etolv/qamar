<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'date' => $this->date,
            'description' => $this->description,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'total' => $this->total,
            'tax' => $this->tax,
            'grand_total' => $this->grand_total,
            'discount' => $this->discount,
            'status' => $this->status?->name,
            'payment_status' => $this->payment_status?->name,
            'employee' => EmployeeResource::make($this->whenLoaded('employee')),
            'coupon' => CouponResource::make($this->whenLoaded('coupon')),
            'addressModel' => AddressResource::make($this->whenLoaded('addressModel')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'services' => ServiceResource::collection($this->whenLoaded('services')),
        ];
    }
}
