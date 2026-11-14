<?php

namespace Tests\Feature;

use App\Models\Taquito;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaquitoApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_taquitos_as_json(): void
    {
        Taquito::factory()->create(['name' => 'Taco al pastor']);

        $this->getJson('/api/taquitos')
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Taco al pastor')
            ->assertJsonPath('data.0.is_available', true);
    }

    public function test_it_creates_a_taquito(): void
    {
        $this->postJson('/api/taquitos', [
            'name' => 'Taco de suadero',
            'description' => 'Con cilantro y cebolla',
            'price' => 25,
            'category' => 'Suadero',
        ])
            ->assertCreated()
            ->assertJsonPath('message', 'Taquito creado correctamente.')
            ->assertJsonPath('data.name', 'Taco de suadero')
            ->assertJsonPath('data.is_available', true);

        $this->assertDatabaseHas('taquitos', [
            'name' => 'Taco de suadero',
            'price' => 25,
        ]);
    }

    public function test_it_rejects_invalid_taquito_data(): void
    {
        $this->postJson('/api/taquitos', [
            'name' => '',
            'price' => 0,
            'category' => '',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'price', 'category'])
            ->assertJsonPath('errors.name.0', 'El nombre del taquito es obligatorio.');
    }

    public function test_it_shows_updates_and_deletes_a_taquito(): void
    {
        $taquito = Taquito::factory()->create(['name' => 'Taco de bistec']);

        $this->getJson("/api/taquitos/{$taquito->id}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Taco de bistec');

        $this->patchJson("/api/taquitos/{$taquito->id}", [
            'price' => 32.5,
            'is_available' => false,
        ])
            ->assertOk()
            ->assertJsonPath('data.price', 32.5)
            ->assertJsonPath('data.is_available', false);

        $this->deleteJson("/api/taquitos/{$taquito->id}")
            ->assertOk()
            ->assertJsonPath('message', 'Taquito eliminado correctamente.');

        $this->assertDatabaseMissing('taquitos', ['id' => $taquito->id]);
    }

    public function test_a_missing_taquito_returns_a_json_not_found_response(): void
    {
        $this->getJson('/api/taquitos/999')
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    }

    public function test_database_seeder_can_run_twice_without_creating_duplicates(): void
    {
        $this->seed();
        $this->seed();

        $this->assertDatabaseCount('taquitos', 3);
        $this->assertDatabaseHas('taquitos', ['name' => 'Taco al pastor']);
    }
}
