<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderStockResource extends JsonResource
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
            'price' => $this->price,
            'quantity' => $this->quantity,
            'type' => $this->type->name,
            'stock' => StockResource::make($this->whenLoaded('stock')),
            'product' => ProductResource::make($this->whenLoaded('product')),
            'order' => OrderResource::make($this->whenLoaded('order')),
        ];
    }
}
