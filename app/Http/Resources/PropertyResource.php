<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'rooms' => $this->rooms,
            'surface' => $this->surface,
            'price' => $this->price,
            'city' => $this->city,
            'neighborhood' => $this->neighborhood,
            'description' => $this->description,
            'status' => $this->status,
            'published' => $this->published,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'role' => $this->user->role
            ],
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at->format('d/m/Y H:i'),
            'updated_at' => $this->updated_at->format('d/m/Y H:i')
        ];
    }
}
