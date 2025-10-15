<?php

namespace App\Http\Resources;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use PhpParser\Node\Expr\FuncCall;

class ProductResource extends JsonResource
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
            'sku' => $this->sku,
            'name' => $this->name,
            'department' => $this->department?->name,
            'price' => $this->stocks()->latest()->first()?->price,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'brand' => BrandResource::make($this->whenLoaded('brand')),
            'stocks' => StockResource::collection($this->whenLoaded('stocks')),
            'stock' => StockResource::make($this->whenLoaded('stock')),
            'image' => $this->getFirstMediaUrl('image'),
            // 'images' => $this->images(),
        ];
    }

    public function images(): array
    {
        $images = [];
        foreach ($this->getMedia('images') as $image) {
            $images[] = $image->getUrl();
        }
        return $images;
    }
}
