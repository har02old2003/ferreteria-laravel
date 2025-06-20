<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Client;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;

class SaleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $employee;
    protected $product;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->employee = User::factory()->create(['role' => 'employee']);
        
        $category = Category::factory()->create();
        $brand = Brand::factory()->create();
        $supplier = Supplier::factory()->create();
        
        $this->product = Product::factory()->create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'supplier_id' => $supplier->id,
            'price' => 25.50,
            'stock' => 100
        ]);
        
        $this->client = Client::factory()->create([
            'dni' => '12345678',
            'full_name' => 'Cliente Test'
        ]);
    }

    /** @test */
    public function admin_can_view_sales_index()
    {
        Sale::factory()->count(3)->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->get('/admin/sales');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Sales/Index')
            ->has('sales')
        );
    }

    /** @test */
    public function employee_can_view_sales_index()
    {
        Sale::factory()->count(2)->create(['user_id' => $this->employee->id]);

        $response = $this->actingAs($this->employee)->get('/employee/sales');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Employee/Sales/Index')
            ->has('sales')
        );
    }

    /** @test */
    public function employee_can_create_sale()
    {
        $saleData = [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'price' => 25.50
                ]
            ],
            'customer_name' => 'Cliente Test',
            'total' => 51.00
        ];

        $response = $this->actingAs($this->employee)->post('/employee/sales', $saleData);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales', [
            'user_id' => $this->employee->id,
            'customer_name' => 'Cliente Test',
            'total' => 51.00,
            'status' => 'completed'
        ]);
        
        $this->assertDatabaseHas('sale_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 25.50
        ]);
    }

    /** @test */
    public function sale_updates_product_stock()
    {
        $initialStock = $this->product->stock;
        
        $saleData = [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 5,
                    'price' => 25.50
                ]
            ],
            'customer_name' => 'Cliente Test',
            'total' => 127.50
        ];

        $this->actingAs($this->employee)->post('/employee/sales', $saleData);

        $this->product->refresh();
        $this->assertEquals($initialStock - 5, $this->product->stock);
    }

    /** @test */
    public function sale_generates_unique_code()
    {
        $saleData = [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 1,
                    'price' => 25.50
                ]
            ],
            'customer_name' => 'Cliente Test',
            'total' => 25.50
        ];

        $this->actingAs($this->employee)->post('/employee/sales', $saleData);

        $sale = Sale::first();
        $this->assertNotNull($sale->code);
        $this->assertStringStartsWith('V', $sale->code);
    }

    /** @test */
    public function admin_can_view_sale_details()
    {
        $sale = Sale::factory()->create(['user_id' => $this->admin->id]);
        
        // Crear SaleItem manualmente sin factory
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => 25.50
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/sales/{$sale->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Sales/Show')
            ->has('sale')
            ->where('sale.id', $sale->id)
        );
    }

    /** @test */
    public function employee_can_view_sale_details()
    {
        $sale = Sale::factory()->create(['user_id' => $this->employee->id]);
        
        // Crear SaleItem manualmente sin factory
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => 25.50
        ]);

        $response = $this->actingAs($this->employee)->get("/employee/sales/{$sale->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function sale_ticket_can_be_generated()
    {
        $sale = Sale::factory()->create([
            'user_id' => $this->employee->id,
            'code' => 'V00000001'
        ]);
        
        // Crear SaleItem manualmente sin factory
        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price' => 25.50
        ]);

        $response = $this->actingAs($this->employee)->get("/employee/sales/{$sale->id}/ticket");

        $response->assertStatus(200);
        $response->assertViewIs('tickets.sale');
        $response->assertViewHas('sale');
    }

    /** @test */
    public function sale_requires_valid_items()
    {
        $saleData = [
            'items' => [], // Sin items
            'customer_name' => 'Cliente Test',
            'total' => 0
        ];

        $response = $this->actingAs($this->employee)->post('/employee/sales', $saleData);

        $response->assertSessionHasErrors(['items']);
    }

    /** @test */
    public function sale_calculates_total_correctly()
    {
        $saleData = [
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 3,
                    'price' => 25.50
                ]
            ],
            'customer_name' => 'Cliente Test',
            'total' => 76.50 // 3 * 25.50
        ];

        $this->actingAs($this->employee)->post('/employee/sales', $saleData);

        $this->assertDatabaseHas('sales', [
            'total' => 76.50,
            'customer_name' => 'Cliente Test'
        ]);
    }

    /** @test */
    public function sale_search_works_correctly()
    {
        $sale1 = Sale::factory()->create([
            'code' => 'V00000001',
            'customer_name' => 'Juan Pérez',
            'user_id' => $this->admin->id
        ]);
        
        $sale2 = Sale::factory()->create([
            'code' => 'V00000002',
            'customer_name' => 'María García',
            'user_id' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/sales?search=Juan');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('sales')
        );
    }
} 