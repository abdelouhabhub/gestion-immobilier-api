<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:Appartement,Villa,Terrain,Studio,Duplex',
            'rooms' => 'nullable|integer|min:1',
            'surface' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
            'city' => 'required|string|max:255',
            'neighborhood' => 'nullable|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:disponible,vendu,location',
            'published' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Le type de bien est obligatoire',
            'type.in' => 'Le type doit être: Appartement, Villa, Terrain, Studio ou Duplex',
            'surface.required' => 'La surface est obligatoire',
            'surface.numeric' => 'La surface doit être un nombre',
            'price.required' => 'Le prix est obligatoire',
            'price.numeric' => 'Le prix doit être un nombre',
            'city.required' => 'La ville est obligatoire',
            'description.required' => 'La description est obligatoire',
            'status.required' => 'Le statut est obligatoire',
            'status.in' => 'Le statut doit être: disponible, vendu ou location'
        ];
    }
}
