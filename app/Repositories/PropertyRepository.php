<?php

namespace App\Repositories;

use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Models\Property;
use App\DTOs\FilterPropertiesDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PropertyRepository implements PropertyRepositoryInterface
{
    public function getAllFiltered(FilterPropertiesDTO $filters): LengthAwarePaginator
    {
        $query = Property::with(['user', 'images']);

        if ($filters->city) {
            $query->where('city', $filters->city);
        }

        if ($filters->type) {
            $query->where('type', $filters->type);
        }

        if ($filters->minPrice) {
            $query->where('price', '>=', $filters->minPrice);
        }

        if ($filters->maxPrice) {
            $query->where('price', '<=', $filters->maxPrice);
        }

        if ($filters->status) {
            $query->where('status', $filters->status);
        }

        if ($filters->search) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'like', "%{$filters->search}%")
                    ->orWhere('description', 'like', "%{$filters->search}%");
            });
        }

        return $query->paginate($filters->perPage);
    }

    public function findById(int $id): ?Property
    {
        return Property::with(['user', 'images'])->find($id);
    }

    public function create(array $data): Property
    {
        return Property::create($data);
    }

    public function update(Property $property, array $data): Property
    {
        $property->update($data);
        return $property->fresh();
    }

    public function delete(Property $property): bool
    {
        return $property->delete();
    }

    public function getByUserId(int $userId): Collection
    {
        return Property::where('user_id', $userId)->get();
    }
}
