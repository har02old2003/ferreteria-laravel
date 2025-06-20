<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Category;
use App\Models\Supplier;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
        ]);

        // Crear categorías de ejemplo
        $categories = [
            ['name' => 'Herramientas Manuales', 'description' => 'Herramientas manuales para construcción y reparación', 'is_active' => true],
            ['name' => 'Materiales de Construcción', 'description' => 'Materiales para construcción y obra', 'is_active' => true],
            ['name' => 'Pinturas y Acabados', 'description' => 'Pinturas, barnices y productos para acabados', 'is_active' => true],
            ['name' => 'Electricidad', 'description' => 'Material eléctrico y accesorios', 'is_active' => true],
            ['name' => 'Fontanería', 'description' => 'Materiales y accesorios para fontanería', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }

        // Crear marcas de ejemplo
        $brands = [
            ['name' => 'Bosch', 'description' => 'Marca reconocida de herramientas eléctricas', 'is_active' => true],
            ['name' => 'Stanley', 'description' => 'Herramientas manuales y accesorios', 'is_active' => true],
            ['name' => 'Sherwin-Williams', 'description' => 'Pinturas y recubrimientos', 'is_active' => true],
            ['name' => 'Legrand', 'description' => 'Material eléctrico y sistemas', 'is_active' => true],
            ['name' => 'Tigre', 'description' => 'Materiales para fontanería', 'is_active' => true],
        ];

        foreach ($brands as $brand) {
            Brand::firstOrCreate(['name' => $brand['name']], $brand);
        }

        // Crear proveedores de ejemplo
        $suppliers = [
            [
                'name' => 'Ferretería Central',
                'contact_name' => 'Juan Martínez',
                'email' => 'juan.martinez@ferreteria.com',
                'phone' => '555-1000',
                'address' => 'Calle Principal 123',
                'is_active' => true
            ],
            [
                'name' => 'Distribuciones López',
                'contact_name' => 'Ana López',
                'email' => 'ana.lopez@distribuciones.com',
                'phone' => '555-2000',
                'address' => 'Avenida Secundaria 456',
                'is_active' => true
            ],
            [
                'name' => 'Suministros Eléctricos',
                'contact_name' => 'Carlos Pérez',
                'email' => 'carlos.perez@suministros.com',
                'phone' => '555-3000',
                'address' => 'Boulevard Industrial 789',
                'is_active' => true
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::firstOrCreate(['name' => $supplier['name']], $supplier);
        }

        // Obtener las colecciones creadas
        $categoriesCollection = Category::all();
        $brandsCollection = Brand::all();
        $suppliersCollection = Supplier::all();

        // Productos sin fecha de caducidad
        $products = Product::factory(15)->create([
            'category_id' => fn() => $categoriesCollection->random()->id,
            'brand_id' => fn() => $brandsCollection->random()->id,
            'supplier_id' => fn() => $suppliersCollection->random()->id,
            'has_expiration' => false,
            'expiration_date' => null,
        ]);

        // Productos con fecha de caducidad
        $expiringProducts = Product::factory(5)->create([
            'category_id' => fn() => $categoriesCollection->random()->id,
            'brand_id' => fn() => $brandsCollection->random()->id,
            'supplier_id' => fn() => $suppliersCollection->random()->id,
            'has_expiration' => true,
            'expiration_date' => fn() => now()->addMonths(rand(1, 6)), // Entre 1 y 6 meses
        ]);

        // Combinar todos los productos
        $allProducts = $products->merge($expiringProducts);

        // Ventas
        Sale::factory(15)->create([
            'user_id' => User::first()->id,
        ])->each(function ($sale) use ($allProducts) {
            // Agregar productos aleatorios a cada venta
            $saleProducts = $allProducts->random(rand(1, 5));
            foreach ($saleProducts as $product) {
                $sale->products()->attach($product->id, [
                    'quantity' => rand(1, 5),
                    'price' => $product->price
                ]);
                // Actualizar el total de la venta
                $sale->update([
                    'total' => $sale->products()->sum(\DB::raw('sale_product.quantity * sale_product.price'))
                ]);
            }
        });
    }
}
