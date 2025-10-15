<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'job' => JobResource::make($this->whenLoaded('job')),
            'user' => UserResource::make($this->user),
        ];
    }
}
