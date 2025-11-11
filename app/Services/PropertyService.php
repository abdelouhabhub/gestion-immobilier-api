<?php

namespace App\Services;

use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\DTOs\CreatePropertyDTO;
use App\DTOs\UpdatePropertyDTO;
use App\DTOs\FilterPropertiesDTO;
use App\Models\Property;
use Illuminate\Pagination\LengthAwarePaginator;

class PropertyService
{
    public function __construct(
        private PropertyRepositoryInterface $repository
    ) {}

    public function getAllProperties(FilterPropertiesDTO $filters): LengthAwarePaginator
    {
        return $this->repository->getAllFiltered($filters);
    }

    public function getPropertyById(int $id): ?Property
    {
        return $this->repository->findById($id);
    }

    public function createProperty(CreatePropertyDTO $dto): Property
    {
        return $this->repository->create($dto->toArray());
    }

    public function updateProperty(Property $property, UpdatePropertyDTO $dto): Property
    {
        return $this->repository->update($property, $dto->toArray());
    }

    public function deleteProperty(Property $property): bool
    {
        return $this->repository->delete($property);
    }
}
