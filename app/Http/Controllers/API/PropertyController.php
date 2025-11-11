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

/**
 * @group Biens Immobiliers
 *
 * Gestion complète des biens immobiliers
 */
class PropertyController extends Controller
{

    use AuthorizesRequests;
    public function __construct(
        private PropertyService $service
    ) {}

    /**
     * List Properties
     *
     * Obtenir la liste paginée des biens
     *
     * @queryParam city string Filtrer par ville. Example: Alger
     * @queryParam type string Type de bien. Example: Villa
     * @queryParam min_price number Prix minimum. Example: 20000000
     * @queryParam max_price number Prix maximum. Example: 50000000
     * @queryParam status string Statut. Example: disponible
     * @queryParam search string Recherche. Example: piscine
     *
     * @response 200 {
     *   "data": [...]
     * }
     */
    public function index(Request $request)
    {
        $filters = FilterPropertiesDTO::fromRequest($request->all());
        $properties = $this->service->getAllProperties($filters);

        return PropertyResource::collection($properties);
    }

    /**
     * Show Property
     *
     * Afficher le détail d'un bien
     *
     * @urlParam id integer required ID du bien. Example: 1
     *
     * @response 200 {
     *   "data": {...}
     * }
     */
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

    /**
     * Create Property
     *
     * Créer un nouveau bien (Agent/Admin)
     *
     * @authenticated
     *
     * @bodyParam type string required Type (Villa, Appartement, etc). Example: Villa
     * @bodyParam rooms integer Nombre de pièces. Example: 4
     * @bodyParam surface number required Surface en m². Example: 200
     * @bodyParam price number required Prix en DZD. Example: 25000000
     * @bodyParam city string required Ville. Example: Alger
     * @bodyParam neighborhood string Quartier. Example: Hydra
     * @bodyParam description string required Description. Example: Belle villa
     * @bodyParam status string required Statut. Example: disponible
     * @bodyParam published boolean Publié. Example: true
     *
     * @response 201 {
     *   "success": true,
     *   "data": {...}
     * }
     */
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

    /**
     * Update Property
     *
     * Modifier un bien (Propriétaire/Admin)
     *
     * @authenticated
     *
     * @urlParam id integer required ID du bien. Example: 1
     */
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

    /**
     * Delete Property
     *
     * Supprimer un bien (soft delete)
     *
     * @authenticated
     *
     * @urlParam id integer required ID du bien. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Bien supprimé"
     * }
     */
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
