<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $employee;
    protected $category;
    protected $brand;
    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->employee = User::factory()->create(['role' => 'employee']);
        
        $this->category = Category::factory()->create();
        $this->brand = Brand::factory()->create();
        $this->supplier = Supplier::factory()->create();
    }

    /** @test */
    public function admin_can_view_products_index()
    {
        Product::factory()->count(5)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'supplier_id' => $this->supplier->id
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/products');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Products/Index')
            ->has('products', 5)
        );
    }

    /** @test */
    public function employee_can_view_products_index()
    {
        Product::factory()->count(3)->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'supplier_id' => $this->supplier->id
        ]);

        $response = $this->actingAs($this->employee)->get('/employee/products');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Employee/Products/Index')
            ->has('products')
        );
    }

    /** @test */
    public function admin_can_create_product()
    {
        $productData = [
            'name' => 'Producto Test',
            'sku' => 'TEST-001',
            'description' => 'Descripción del producto test',
            'price' => 25.50,
            'stock' => 100,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'supplier_id' => $this->supplier->id,
            'is_active' => true,
            'has_expiration' => false
        ];

        $response = $this->actingAs($this->admin)->post('/admin/products', $productData);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'name' => 'Producto Test',
            'sku' => 'TEST-001',
            'price' => 25.50
        ]);
    }

    /** @test */
    public function employee_cannot_create_product()
    {
        $productData = [
            'name' => 'Producto Test',
            'sku' => 'TEST-001',
            'price' => 25.50
        ];

        $response = $this->actingAs($this->employee)->post('/admin/products', $productData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('products', ['name' => 'Producto Test']);
    }

    /** @test */
    public function admin_can_update_product()
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'supplier_id' => $this->supplier->id
        ]);

        $updateData = [
            'name' => 'Producto Actualizado',
            'sku' => $product->sku,
            'price' => 30.00,
            'stock' => 150,
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'supplier_id' => $this->supplier->id,
            'is_active' => true,
            'has_expiration' => false
        ];

        $response = $this->actingAs($this->admin)->put("/admin/products/{$product->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Producto Actualizado',
            'price' => 30.00
        ]);
    }

    /** @test */
    public function admin_can_delete_product()
    {
        $product = Product::factory()->create([
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'supplier_id' => $this->supplier->id
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/products/{$product->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /** @test */
    public function product_search_works_correctly()
    {
        Product::factory()->create([
            'name' => 'Martillo Grande',
            'sku' => 'MAR-001',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'supplier_id' => $this->supplier->id
        ]);

        Product::factory()->create([
            'name' => 'Destornillador',
            'sku' => 'DES-001',
            'category_id' => $this->category->id,
            'brand_id' => $this->brand->id,
            'supplier_id' => $this->supplier->id
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/products?search=Martillo');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('products', 1)
        );
    }

    /** @test */
    public function product_requires_valid_data()
    {
        $response = $this->actingAs($this->admin)->post('/admin/products', []);

        $response->assertSessionHasErrors(['name', 'price', 'stock']);
    }

    /** @test */
    public function product_price_must_be_numeric()
    {
        $productData = [
            'name' => 'Producto Test',
            'price' => 'no-numerico',
            'stock' => 10
        ];

        $response = $this->actingAs($this->admin)->post('/admin/products', $productData);

        $response->assertSessionHasErrors(['price']);
    }

    /** @test */
    public function product_stock_must_be_integer()
    {
        $productData = [
            'name' => 'Producto Test',
            'price' => 25.50,
            'stock' => 'no-entero'
        ];

        $response = $this->actingAs($this->admin)->post('/admin/products', $productData);

        $response->assertSessionHasErrors(['stock']);
    }
} 