<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'total' => $this->total,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'stockItems' => PackageItemResource::collection($this->whenLoaded('stockItems')),
            'serviceItems' => PackageItemResource::collection($this->whenLoaded('serviceItems')),
        ];
    }
}
