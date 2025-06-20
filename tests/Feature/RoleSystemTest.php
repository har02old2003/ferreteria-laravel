<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RoleSystemTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $employee;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuarios con roles directos (no tabla pivote)
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->employee = User::factory()->create(['role' => 'employee']);
    }

    /** @test */
    public function admin_can_access_admin_dashboard()
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');

        // La ruta admin/dashboard redirige al dashboard unificado
        $response->assertRedirect('/dashboard');
        
        // Probar el dashboard unificado directamente
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }

    /** @test */
    public function employee_can_access_employee_dashboard()
    {
        $response = $this->actingAs($this->employee)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }

    /** @test */
    public function admin_can_access_admin_routes()
    {
        $routes = [
            '/admin/products',
            '/admin/categories',
            '/admin/brands',
            '/admin/suppliers',
            '/admin/sales',
            '/admin/clients',
            '/admin/users'
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            $response->assertStatus(200);
        }
    }

    /** @test */
    public function employee_cannot_access_admin_routes()
    {
        $adminRoutes = [
            '/admin/products',
            '/admin/categories',
            '/admin/brands',
            '/admin/suppliers',
            '/admin/users'
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($this->employee)->get($route);
            $response->assertStatus(403);
        }
    }

    /** @test */
    public function employee_can_access_employee_routes()
    {
        $employeeRoutes = [
            '/employee/products',
            '/employee/sales',
            '/employee/sales/create'
        ];

        foreach ($employeeRoutes as $route) {
            $response = $this->actingAs($this->employee)->get($route);
            $response->assertStatus(200);
        }
    }

    /** @test */
    public function admin_cannot_access_employee_specific_routes()
    {
        // Los administradores pueden acceder a rutas de empleado
        // pero no deberían tener acceso específico a funciones de empleado
        $response = $this->actingAs($this->admin)->get('/employee/products');
        
        // Esto debería redirigir o dar 403 según la lógica de negocio
        $this->assertTrue(
            $response->status() === 403 || $response->status() === 302
        );
    }

    /** @test */
    public function unauthenticated_user_redirected_to_login()
    {
        $routes = [
            '/dashboard',
            '/admin/dashboard',
            '/employee/products',
            '/admin/products'
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }
} 