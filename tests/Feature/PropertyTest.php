<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PropertyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    public function test_guest_can_view_properties_list(): void
    {
        $user = User::factory()->create(['role' => 'agent']);
        Property::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/properties');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'type', 'price', 'city']
                ]
            ]);
    }

    public function test_guest_can_view_single_property(): void
    {
        $user = User::factory()->create(['role' => 'agent']);
        $property = Property::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/properties/{$property->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $property->id,
                    'title' => $property->title
                ]
            ]);
    }

    public function test_agent_can_create_property(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);

        $response = $this->actingAs($agent, 'sanctum')
            ->postJson('/api/properties', [
                'type' => 'Villa',
                'rooms' => 4,
                'surface' => 200,
                'price' => 25000000,
                'city' => 'Alger',
                'neighborhood' => 'Hydra',
                'description' => 'Belle villa moderne',
                'status' => 'disponible',
                'published' => true
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Bien immobilier créé avec succès'
            ]);

        $this->assertDatabaseHas('properties', [
            'type' => 'Villa',
            'city' => 'Alger'
        ]);
    }

    public function test_agent_can_update_own_property(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $property = Property::factory()->create(['user_id' => $agent->id]);

        $response = $this->actingAs($agent, 'sanctum')
            ->putJson("/api/properties/{$property->id}", [
                'type' => 'Appartement',
                'rooms' => 3,
                'surface' => 120,
                'price' => 15000000,
                'city' => 'Oran',
                'description' => 'Appartement rénové',
                'status' => 'disponible',
                'published' => true
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'city' => 'Oran'
        ]);
    }

    public function test_agent_cannot_update_other_agent_property(): void
    {
        $agent1 = User::factory()->create(['role' => 'agent']);
        $agent2 = User::factory()->create(['role' => 'agent']);
        $property = Property::factory()->create(['user_id' => $agent1->id]);

        $response = $this->actingAs($agent2, 'sanctum')
            ->putJson("/api/properties/{$property->id}", [
                'price' => 10000000,
                'type' => 'Villa',
                'surface' => 200,
                'city' => 'Alger',
                'description' => 'Test',
                'status' => 'disponible'
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_any_property(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $agent = User::factory()->create(['role' => 'agent']);
        $property = Property::factory()->create(['user_id' => $agent->id]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/properties/{$property->id}", [
                'type' => 'Villa',
                'rooms' => 5,
                'surface' => 250,
                'price' => 30000000,
                'city' => 'Constantine',
                'description' => 'Modifié par admin',
                'status' => 'disponible',
                'published' => true
            ]);

        $response->assertStatus(200);
    }

    public function test_agent_can_delete_own_property(): void
    {
        $agent = User::factory()->create(['role' => 'agent']);
        $property = Property::factory()->create(['user_id' => $agent->id]);

        $response = $this->actingAs($agent, 'sanctum')
            ->deleteJson("/api/properties/{$property->id}");

        $response->assertStatus(200);

        $this->assertSoftDeleted('properties', [
            'id' => $property->id
        ]);
    }

    public function test_guest_cannot_create_property(): void
    {
        $guest = User::factory()->create(['role' => 'guest']);

        $response = $this->actingAs($guest, 'sanctum')
            ->postJson('/api/properties', [
                'type' => 'Villa',
                'rooms' => 4,
                'surface' => 200,
                'price' => 25000000,
                'city' => 'Alger',
                'description' => 'Test',
                'status' => 'disponible'
            ]);

        $response->assertStatus(403);
    }

    public function test_properties_can_be_filtered_by_city(): void
    {
        $user = User::factory()->create(['role' => 'agent']);
        Property::factory()->create(['user_id' => $user->id, 'city' => 'Alger']);
        Property::factory()->create(['user_id' => $user->id, 'city' => 'Oran']);

        $response = $this->getJson('/api/properties?city=Alger');

        $response->assertStatus(200);
        $data = $response->json('data');

        foreach ($data as $property) {
            $this->assertEquals('Alger', $property['city']);
        }
    }
}
