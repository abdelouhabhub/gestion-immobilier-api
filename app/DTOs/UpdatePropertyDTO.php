<?php

namespace App\DTOs;

class UpdatePropertyDTO
{
    public function __construct(
        public string $type,
        public ?int $rooms,
        public float $surface,
        public float $price,
        public string $city,
        public ?string $neighborhood,
        public string $description,
        public string $status,
        public bool $published
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            type: $data['type'],
            rooms: $data['rooms'] ?? null,
            surface: (float) $data['surface'],
            price: (float) $data['price'],
            city: $data['city'],
            neighborhood: $data['neighborhood'] ?? null,
            description: $data['description'],
            status: $data['status'],
            published: $data['published'] ?? false
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'rooms' => $this->rooms,
            'surface' => $this->surface,
            'price' => $this->price,
            'city' => $this->city,
            'neighborhood' => $this->neighborhood,
            'description' => $this->description,
            'status' => $this->status,
            'published' => $this->published,
        ];
    }
}
