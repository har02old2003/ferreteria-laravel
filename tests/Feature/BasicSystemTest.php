<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Client;
use App\Models\Sale;

class BasicSystemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_access_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function employee_can_access_dashboard()
    {
        $employee = User::factory()->create(['role' => 'employee']);

        $response = $this->actingAs($employee)->get('/dashboard');

        $response->assertStatus(200);
    }

    /** @test */
    public function can_create_basic_models()
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();
        $supplier = Supplier::factory()->create();
        $client = Client::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('brands', ['id' => $brand->id]);
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id]);
        $this->assertDatabaseHas('clients', ['id' => $client->id]);
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin']);
    }

    /** @test */
    public function can_create_product_with_relations()
    {
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();
        $supplier = Supplier::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'supplier_id' => $supplier->id
        ]);

        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->assertEquals($category->id, $product->category_id);
        $this->assertEquals($brand->id, $product->brand_id);
        $this->assertEquals($supplier->id, $product->supplier_id);
    }

    /** @test */
    public function can_create_sale_with_user()
    {
        $user = User::factory()->create(['role' => 'employee']);

        $sale = Sale::factory()->create(['user_id' => $user->id]);

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'user_id' => $user->id
        ]);
    }

    /** @test */
    public function admin_can_access_admin_routes()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/products');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/admin/sales');
        $response->assertStatus(200);

        $response = $this->actingAs($admin)->get('/admin/clients');
        $response->assertStatus(200);
    }

    /** @test */
    public function employee_can_access_employee_routes()
    {
        $employee = User::factory()->create(['role' => 'employee']);

        $response = $this->actingAs($employee)->get('/employee/products');
        $response->assertStatus(200);

        $response = $this->actingAs($employee)->get('/employee/sales');
        $response->assertStatus(200);
    }

    /** @test */
    public function employee_cannot_access_admin_routes()
    {
        $employee = User::factory()->create(['role' => 'employee']);

        $response = $this->actingAs($employee)->get('/admin/users');
        $response->assertStatus(403);

        $response = $this->actingAs($employee)->get('/admin/clients');
        $response->assertStatus(403);
    }
} 