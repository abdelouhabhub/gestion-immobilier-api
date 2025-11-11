<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\User;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $agents = User::whereIn('role', ['agent', 'admin'])->get();

        $cities = ['Alger', 'Oran', 'Constantine', 'Annaba', 'Blida', 'Sétif', 'Batna', 'Tlemcen'];
        $neighborhoods = [
            'Alger' => ['Bab Ezzouar', 'Hydra', 'El Biar', 'Bir Mourad Raïs', 'Dely Ibrahim', 'Cheraga'],
            'Oran' => ['Es Senia', 'Arzew', 'Bir El Djir', 'Sidi Chami'],
            'Constantine' => ['Zouaghi', 'Belle Vue', 'Ciloc'],
        ];
        $types = ['Appartement', 'Villa', 'Studio', 'Duplex', 'Terrain'];
        $statuses = ['disponible', 'vendu', 'location'];

        $properties = [
            [
                'type' => 'Villa',
                'rooms' => 6,
                'surface' => 350,
                'price' => 45000000,
                'city' => 'Alger',
                'neighborhood' => 'Hydra',
                'description' => 'Magnifique villa moderne avec piscine et jardin. Vue panoramique sur la mer. Quartier calme et sécurisé.',
                'status' => 'disponible',
                'published' => true
            ],
            [
                'type' => 'Appartement',
                'rooms' => 4,
                'surface' => 120,
                'price' => 18000000,
                'city' => 'Alger',
                'neighborhood' => 'Bab Ezzouar',
                'description' => 'Appartement F4 récent avec parking et ascenseur. Proche de toutes commodités.',
                'status' => 'disponible',
                'published' => true
            ],
            [
                'type' => 'Appartement',
                'rooms' => 3,
                'surface' => 85,
                'price' => 12000000,
                'city' => 'Oran',
                'neighborhood' => 'Es Senia',
                'description' => 'F3 bien situé, ensoleillé, idéal pour famille.',
                'status' => 'disponible',
                'published' => true
            ],
            [
                'type' => 'Villa',
                'rooms' => 5,
                'surface' => 280,
                'price' => 35000000,
                'city' => 'Constantine',
                'neighborhood' => 'Belle Vue',
                'description' => 'Villa spacieuse avec grand jardin et garage double.',
                'status' => 'vendu',
                'published' => true
            ],
            [
                'type' => 'Studio',
                'rooms' => 1,
                'surface' => 35,
                'price' => 4500000,
                'city' => 'Alger',
                'neighborhood' => 'El Biar',
                'description' => 'Studio meublé, idéal pour étudiant ou célibataire.',
                'status' => 'location',
                'published' => true
            ],
            [
                'type' => 'Duplex',
                'rooms' => 5,
                'surface' => 180,
                'price' => 28000000,
                'city' => 'Alger',
                'neighborhood' => 'Dely Ibrahim',
                'description' => 'Duplex moderne avec terrasse, standing élevé.',
                'status' => 'disponible',
                'published' => true
            ],
            [
                'type' => 'Terrain',
                'rooms' => null,
                'surface' => 500,
                'price' => 15000000,
                'city' => 'Blida',
                'neighborhood' => null,
                'description' => 'Terrain constructible 500m², bien situé, acte notarié.',
                'status' => 'disponible',
                'published' => true
            ],
            [
                'type' => 'Appartement',
                'rooms' => 2,
                'surface' => 65,
                'price' => 8500000,
                'city' => 'Annaba',
                'neighborhood' => 'Centre-ville',
                'description' => 'F2 en centre-ville, proche marché et transports.',
                'status' => 'disponible',
                'published' => false
            ],
        ];

        foreach ($properties as $propertyData) {
            Property::create([
                'user_id' => $agents->random()->id,
                'type' => $propertyData['type'],
                'rooms' => $propertyData['rooms'],
                'surface' => $propertyData['surface'],
                'price' => $propertyData['price'],
                'city' => $propertyData['city'],
                'neighborhood' => $propertyData['neighborhood'],
                'description' => $propertyData['description'],
                'status' => $propertyData['status'],
                'published' => $propertyData['published']
            ]);
        }

        // Ajouter quelques propriétés aléatoires supplémentaires
        for ($i = 0; $i < 12; $i++) {
            $city = $cities[array_rand($cities)];
            $neighborhood = $neighborhoods[$city] ?? null;

            Property::create([
                'user_id' => $agents->random()->id,
                'type' => $types[array_rand($types)],
                'rooms' => rand(1, 6),
                'surface' => rand(50, 300),
                'price' => rand(5000000, 50000000),
                'city' => $city,
                'neighborhood' => $neighborhood ? $neighborhood[array_rand($neighborhood)] : null,
                'description' => 'Bien immobilier de qualité dans un quartier recherché.',
                'status' => $statuses[array_rand($statuses)],
                'published' => (bool)rand(0, 1)
            ]);
        }
    }
}
