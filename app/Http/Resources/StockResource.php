<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
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
            'barcode' => $this->barcode,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'expiration_date' => $this->expiration_date,
            'unit' => UnitResource::make($this->whenLoaded('unit')),
            'product' => ProductResource::make($this->whenLoaded('product')),
        ];
    }
}
