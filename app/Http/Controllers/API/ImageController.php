<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Image;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function upload(Request $request, $propertyId)
    {
        $property = Property::findOrFail($propertyId);

        // Vérifier que l'utilisateur a le droit de modifier ce bien
        $this->authorize('update', $property);

        $request->validate([
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
        ], [
            'images.required' => 'Au moins une image est requise',
            'images.*.image' => 'Le fichier doit être une image',
            'images.*.mimes' => 'Formats autorisés: jpeg, png, jpg, webp',
            'images.*.max' => 'Taille maximale: 2MB par image'
        ]);

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('properties', 'public');

            $imageModel = Image::create([
                'property_id' => $property->id,
                'path' => $path
            ]);

            $uploadedImages[] = [
                'id' => $imageModel->id,
                'path' => asset('storage/' . $imageModel->path)
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Images téléchargées avec succès',
            'data' => $uploadedImages
        ], 201);
    }
}
