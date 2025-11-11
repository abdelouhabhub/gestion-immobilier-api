<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\PropertyService;
use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\DTOs\CreatePropertyDTO;
use App\DTOs\UpdatePropertyDTO;
use App\DTOs\FilterPropertiesDTO;
use App\Models\Property;
use Illuminate\Http\Request;

class PropertyController extends Controller
{

    use AuthorizesRequests;
    public function __construct(
        private PropertyService $service
    ) {}

    public function index(Request $request)
    {
        $filters = FilterPropertiesDTO::fromRequest($request->all());
        $properties = $this->service->getAllProperties($filters);

        return PropertyResource::collection($properties);
    }

    public function show($id)
    {
        $property = $this->service->getPropertyById($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Bien immobilier non trouvé'
            ], 404);
        }

        #$this->authorize('view', $property);

        return new PropertyResource($property);
    }

    public function store(StorePropertyRequest $request)
    {
        $this->authorize('create', Property::class);

        $dto = CreatePropertyDTO::fromRequest(
            $request->validated(),
            $request->user()->id
        );

        $property = $this->service->createProperty($dto);

        return response()->json([
            'success' => true,
            'message' => 'Bien immobilier créé avec succès',
            'data' => new PropertyResource($property)
        ], 201);
    }

    public function update(UpdatePropertyRequest $request, $id)
    {
        $property = $this->service->getPropertyById($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Bien immobilier non trouvé'
            ], 404);
        }

        $this->authorize('update', $property);

        $dto = UpdatePropertyDTO::fromRequest($request->validated());
        $property = $this->service->updateProperty($property, $dto);

        return response()->json([
            'success' => true,
            'message' => 'Bien immobilier mis à jour avec succès',
            'data' => new PropertyResource($property)
        ]);
    }

    public function destroy($id)
    {
        $property = $this->service->getPropertyById($id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Bien immobilier non trouvé'
            ], 404);
        }

        $this->authorize('delete', $property);

        $this->service->deleteProperty($property);

        return response()->json([
            'success' => true,
            'message' => 'Bien immobilier supprimé avec succès'
        ]);
    }
}
