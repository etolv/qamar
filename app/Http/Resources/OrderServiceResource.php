<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderServiceResource extends JsonResource
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
            'type' => $this->type->name,
            'status' => $this->status->name,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'session_count' => $this->session_count,
            'due_date' => $this->due_date,
            'session_price' => $this->session_price,
            'service' => ServiceResource::make($this->whenLoaded('service')),
            'employee' => EmployeeResource::make($this->whenLoaded('employee')),
            'order' => OrderResource::make($this->whenLoaded('order')),
        ];
    }
}
