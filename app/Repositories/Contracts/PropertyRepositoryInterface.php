<?php

namespace App\Repositories\Contracts;

use App\DTOs\FilterPropertiesDTO;
use App\Models\Property;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PropertyRepositoryInterface
{
    public function getAllFiltered(FilterPropertiesDTO $filters): LengthAwarePaginator;
    public function findById(int $id): ?Property;
    public function create(array $data): Property;
    public function update(Property $property, array $data): Property;
    public function delete(Property $property): bool;
    public function getByUserId(int $userId): Collection;
}
