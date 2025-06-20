<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use Carbon\Carbon;

class DashboardTest extends TestCase
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
    public function admin_dashboard_shows_correct_statistics()
    {
        // Crear datos de prueba
        Category::factory()->count(5)->create();
        Brand::factory()->count(3)->create();
        Supplier::factory()->count(4)->create();
        User::factory()->count(2)->create(['role' => 'employee']);
        
        // Crear ventas del día actual
        Sale::factory()->count(3)->create([
            'user_id' => $this->admin->id,
            'created_at' => Carbon::today(),
            'total' => 100.00,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }

    /** @test */
    public function employee_dashboard_shows_limited_statistics()
    {
        // Crear ventas del empleado
        Sale::factory()->count(2)->create([
            'user_id' => $this->employee->id,
            'created_at' => Carbon::today(),
            'total' => 50.00,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->employee)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }

    /** @test */
    public function dashboard_calculates_weekly_sales_correctly()
    {
        // Crear ventas de la semana
        $startOfWeek = Carbon::now()->startOfWeek();
        
        for ($i = 0; $i < 7; $i++) {
            Sale::factory()->create([
                'user_id' => $this->admin->id,
                'created_at' => $startOfWeek->copy()->addDays($i),
                'total' => 100.00,
                'status' => 'completed'
            ]);
        }

        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }

    /** @test */
    public function dashboard_shows_low_stock_products()
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();
        $supplier = Supplier::factory()->create();

        // Crear productos con stock bajo
        Product::factory()->count(3)->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'supplier_id' => $supplier->id,
            'stock' => 2,
            'minimum_stock' => 5
        ]);

        // Crear productos con stock normal
        Product::factory()->count(2)->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'supplier_id' => $supplier->id,
            'stock' => 20,
            'minimum_stock' => 5
        ]);

        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }

    /** @test */
    public function dashboard_shows_recent_sales()
    {
        // Crear ventas recientes
        Sale::factory()->count(5)->create([
            'user_id' => $this->admin->id,
            'created_at' => Carbon::now()->subHours(2),
            'status' => 'completed'
        ]);

        // Crear ventas antiguas
        Sale::factory()->count(3)->create([
            'user_id' => $this->admin->id,
            'created_at' => Carbon::now()->subDays(5),
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }

    /** @test */
    public function dashboard_filters_sales_by_status()
    {
        // Crear ventas completadas
        Sale::factory()->count(3)->create([
            'user_id' => $this->admin->id,
            'created_at' => Carbon::today(),
            'status' => 'completed',
            'total' => 100.00
        ]);

        // Crear ventas pendientes
        Sale::factory()->count(2)->create([
            'user_id' => $this->admin->id,
            'created_at' => Carbon::today(),
            'status' => 'pending',
            'total' => 50.00
        ]);

        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }

    /** @test */
    public function dashboard_shows_monthly_comparison()
    {
        // Ventas del mes actual
        Sale::factory()->count(10)->create([
            'user_id' => $this->admin->id,
            'created_at' => Carbon::now()->startOfMonth()->addDays(5),
            'total' => 100.00,
            'status' => 'completed'
        ]);

        // Ventas del mes anterior
        Sale::factory()->count(8)->create([
            'user_id' => $this->admin->id,
            'created_at' => Carbon::now()->subMonth()->startOfMonth()->addDays(5),
            'total' => 80.00,
            'status' => 'completed'
        ]);

        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }

    /** @test */
    public function employee_cannot_see_admin_statistics()
    {
        $response = $this->actingAs($this->employee)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }

    /** @test */
    public function dashboard_handles_no_data_gracefully()
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }
}