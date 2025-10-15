<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'products' => ProductResource::collection($this->whenLoaded('products')),
            'image' => $this->getFirstMediaUrl('image'),
        ];
    }
}
