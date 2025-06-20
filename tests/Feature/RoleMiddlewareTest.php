<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard()
    {
        // Crear admin con el campo role
        $admin = User::factory()->create(['role' => 'admin']);
        
        Log::info('Admin user created:', [
            'id' => $admin->id,
            'role' => $admin->role,
            'email' => $admin->email
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        Log::info('Response status:', ['status' => $response->status()]);

        // La ruta admin/dashboard redirige al dashboard unificado
        $response->assertRedirect('/dashboard');
        
        // Probar el dashboard unificado directamente
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_employee_cannot_access_admin_dashboard()
    {
        $employee = User::factory()->create(['role' => 'employee']);

        $response = $this->actingAs($employee)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_employee_can_access_employee_routes()
    {
        $employee = User::factory()->create(['role' => 'employee']);

        $response = $this->actingAs($employee)->get('/employee/products');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_is_redirected_to_login()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }
} 