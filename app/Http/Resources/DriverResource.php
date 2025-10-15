<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
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
            'user' => UserResource::make($this->whenLoaded('user')),
            'city' => CityResource::make($this->whenLoaded('city')),
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'nationality' => NationalityResource::make($this->whenLoaded('nationality')),
            'lat' => $this->lat,
            'lng' => $this->lng,
        ];
    }
}
