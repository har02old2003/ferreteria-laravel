<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Http;

class ClientTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->employee = User::factory()->create(['role' => 'employee']);
    }

    /** @test */
    public function admin_can_view_clients_index()
    {
        Client::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get('/admin/clients');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Clients/Index')
            ->has('clients', 5)
        );
    }

    /** @test */
    public function admin_can_create_client()
    {
        $clientData = [
            'dni' => '12345678',
            'first_name' => 'Juan Carlos',
            'last_name' => 'Pérez García',
            'phone' => '987654321',
            'email' => 'juan@example.com',
            'address' => 'Av. Los Olivos 123, Lima'
        ];

        $response = $this->actingAs($this->admin)->post('/admin/clients', $clientData);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'dni' => '12345678',
            'first_name' => 'Juan Carlos',
            'last_name' => 'Pérez García',
            'full_name' => 'Juan Carlos Pérez García'
        ]);
    }

    /** @test */
    public function client_full_name_is_generated_automatically()
    {
        $clientData = [
            'dni' => '12345678',
            'first_name' => 'Juan Carlos',
            'last_name' => 'Pérez García',
            'phone' => '987654321'
        ];

        $this->actingAs($this->admin)->post('/admin/clients', $clientData);

        $client = Client::where('dni', '12345678')->first();
        $this->assertEquals('Juan Carlos Pérez García', $client->full_name);
    }

    /** @test */
    public function admin_can_search_client_by_dni_in_database()
    {
        // Crear cliente en base de datos
        $client = Client::factory()->create([
            'dni' => '12345678',
            'first_name' => 'Juan Carlos',
            'last_name' => 'Pérez García',
            'full_name' => 'Juan Carlos Pérez García'
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/clients/search-dni', [
            'dni' => '12345678'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'client' => [
                'dni' => '12345678',
                'full_name' => 'Juan Carlos Pérez García'
            ],
            'source' => 'database'
        ]);
    }

    /** @test */
    public function employee_can_search_client_by_dni()
    {
        // Crear cliente en base de datos
        $client = Client::factory()->create([
            'dni' => '87654321',
            'first_name' => 'María Elena',
            'last_name' => 'Rodríguez López',
            'full_name' => 'María Elena Rodríguez López'
        ]);

        $response = $this->actingAs($this->employee)->post('/employee/clients/search-dni', [
            'dni' => '87654321'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'client' => [
                'dni' => '87654321',
                'full_name' => 'María Elena Rodríguez López'
            ],
            'source' => 'database'
        ]);
    }

    /** @test */
    public function search_dni_returns_not_found_for_non_existent_client()
    {
        // Mock de la API externa para que falle
        Http::fake([
            'api.reniec-sunat.com/*' => Http::response([], 404),
            'dniruc.apisperu.com/*' => Http::response([], 404),
            'api.perudevs.com/*' => Http::response([], 404)
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/clients/search-dni', [
            'dni' => '99999999'
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false
        ]);
    }

    /** @test */
    public function search_dni_validates_dni_format()
    {
        // DNI muy corto - debe fallar con validación
        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/admin/clients/search-dni', [
                'dni' => '1234'
            ]);
        $response->assertStatus(422); // Unprocessable Entity para errores de validación
        
        // DNI muy largo - debe fallar con validación
        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/admin/clients/search-dni', [
                'dni' => '123456789'
            ]);
        $response->assertStatus(422);
        
        // DNI correcto - debe pasar validación pero no encontrar datos
        Http::fake([
            'api.reniec-sunat.com/*' => Http::response([], 404),
            'dniruc.apisperu.com/*' => Http::response([], 404),
            'api.perudevs.com/*' => Http::response([], 404)
        ]);
        
        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->post('/admin/clients/search-dni', [
                'dni' => '12345678'
            ]);
        $response->assertStatus(404); // No encontrado, pero validación pasó
    }

    /** @test */
    public function search_dni_works_with_api_response()
    {
        // Mock de respuesta exitosa de API
        Http::fake([
            'api.reniec-sunat.com/*' => Http::response([
                'nombres' => 'CARLOS ALBERTO',
                'apellidoPaterno' => 'MENDOZA',
                'apellidoMaterno' => 'SILVA'
            ], 200)
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/clients/search-dni', [
            'dni' => '55667788'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'client' => [
                'dni' => '55667788',
                'first_name' => 'CARLOS ALBERTO',
                'last_name' => 'MENDOZA SILVA',
                'full_name' => 'CARLOS ALBERTO MENDOZA SILVA'
            ],
            'source' => 'api'
        ]);
    }

    /** @test */
    public function client_search_works_correctly()
    {
        Client::factory()->create([
            'dni' => '12345678',
            'first_name' => 'Juan Carlos',
            'last_name' => 'Pérez García',
            'full_name' => 'Juan Carlos Pérez García'
        ]);

        Client::factory()->create([
            'dni' => '87654321',
            'first_name' => 'María Elena',
            'last_name' => 'Rodríguez López',
            'full_name' => 'María Elena Rodríguez López'
        ]);

        // Buscar por DNI
        $response = $this->actingAs($this->admin)->get('/admin/clients?search=12345678');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('clients', 1)
            ->where('clients.0.dni', '12345678')
        );

        // Buscar por nombre
        $response = $this->actingAs($this->admin)->get('/admin/clients?search=María');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('clients', 1)
            ->where('clients.0.first_name', 'María Elena')
        );
    }

    /** @test */
    public function client_requires_valid_data()
    {
        $response = $this->actingAs($this->admin)->post('/admin/clients', []);

        $response->assertSessionHasErrors(['dni', 'first_name', 'last_name']);
    }

    /** @test */
    public function client_dni_must_be_unique()
    {
        Client::factory()->create(['dni' => '12345678']);

        $clientData = [
            'dni' => '12345678', // DNI duplicado
            'first_name' => 'Otro',
            'last_name' => 'Cliente'
        ];

        $response = $this->actingAs($this->admin)->post('/admin/clients', $clientData);

        $response->assertSessionHasErrors(['dni']);
    }

    /** @test */
    public function admin_can_update_client()
    {
        $client = Client::factory()->create([
            'dni' => '12345678',
            'first_name' => 'Juan',
            'last_name' => 'Pérez'
        ]);

        $updateData = [
            'dni' => '12345678',
            'first_name' => 'Juan Carlos',
            'last_name' => 'Pérez García',
            'phone' => '987654321',
            'status' => 'active'
        ];

        $response = $this->actingAs($this->admin)->put("/admin/clients/{$client->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'first_name' => 'Juan Carlos',
            'last_name' => 'Pérez García',
            'full_name' => 'Juan Carlos Pérez García'
        ]);
    }

    /** @test */
    public function admin_can_delete_client()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/admin/clients/{$client->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }
} 