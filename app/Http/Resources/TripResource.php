<?php

namespace App\Http\Resources;

use App\Models\Booking;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
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
            'status' => $this->status->name,
            'from_lng' => $this->from_lng,
            'from_lat' => $this->from_lat,
            'to_lng' => $this->to_lng,
            'to_lat' => $this->to_lat,
            'from' => $this->from,
            'to' => $this->to,
            'description' => $this->description,
            'tripable' => $this->whenLoaded('tripable', function () {
                if ($this->tripable_type == Booking::class) {
                    return BookingResource::make($this->tripable);
                } elseif ($this->tripable_type == Order::class) {
                    return OrderResource::make($this->tripable);
                }
            })
        ];
    }
}
