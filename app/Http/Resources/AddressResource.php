<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'title' => $this->title,
            'address' => $this->address,
            'street' => $this->street,
            'lng' => $this->lng,
            'lat' => $this->lat,
            'image' => $this->getFirstMediaUrl('image'),
            'municipal' => MunicipalResource::make($this->whenLoaded('municipal')),
        ];
    }
}
