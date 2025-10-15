<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'has_terms' => $this->has_terms,
            'image' => $this->getFirstMediaUrl('image'),
            'terms' => $this->getFirstMediaUrl('terms'),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'service' => ServiceResource::make($this->whenLoaded('service')),
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
