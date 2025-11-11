<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        $types = ['Appartement', 'Villa', 'Terrain', 'Studio', 'Duplex'];
        $cities = ['Alger', 'Oran', 'Constantine', 'Annaba'];
        $statuses = ['disponible', 'vendu', 'location'];

        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement($types),
            'rooms' => $this->faker->numberBetween(1, 6),
            'surface' => $this->faker->numberBetween(50, 300),
            'price' => $this->faker->numberBetween(5000000, 50000000),
            'city' => $this->faker->randomElement($cities),
            'neighborhood' => $this->faker->city(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement($statuses),
            'published' => $this->faker->boolean(),
        ];
    }
}
