<?php

namespace App\Http\Resources;

use App\Models\Service;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageItemResource extends JsonResource
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
            'price' => $this->price,
            'quantity' => $this->quantity,
            'type' => $this->type,
            'item' => $this->itemResource(),
        ];
    }

    public function itemResource()
    {
        if ($this->item instanceof Stock) {
            return StockResource::make($this->item);
        } else if ($this->item instanceof Service) {
            return ServiceResource::make($this->item);
        }
        return null;
    }
}
