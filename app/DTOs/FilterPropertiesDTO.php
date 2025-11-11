<?php

namespace App\DTOs;

class FilterPropertiesDTO
{
    public function __construct(
        public ?string $city = null,
        public ?string $type = null,
        public ?float $minPrice = null,
        public ?float $maxPrice = null,
        public ?string $status = null,
        public ?string $search = null,
        public int $perPage = 15
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            city: $data['city'] ?? null,
            type: $data['type'] ?? null,
            minPrice: isset($data['min_price']) ? (float) $data['min_price'] : null,
            maxPrice: isset($data['max_price']) ? (float) $data['max_price'] : null,
            status: $data['status'] ?? null,
            search: $data['search'] ?? null,
            perPage: (int) ($data['per_page'] ?? 15)
        );
    }
}
