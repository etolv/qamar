<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'dial_code' => $this->dial_code,
            'phone' => $this->phone,
            'image' => $this->getFirstMediaUrl('profile'),
            'token' => $this->token,
            'type' => Str::snake(Str::afterLast($this->type_type, '\\'))
        ];
    }
}
