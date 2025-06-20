<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FerrechinchaCompleteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. CATEGORÍAS
        DB::table('categories')->insert([
            ['name' => 'Herramientas Manuales', 'description' => 'Martillos, destornilladores, llaves, alicates', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Herramientas Eléctricas', 'description' => 'Taladros, sierras eléctricas, lijadoras', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Materiales de Construcción', 'description' => 'Cemento, arena, ladrillos', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Fontanería', 'description' => 'Tuberías, llaves, grifos', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Electricidad', 'description' => 'Cables, interruptores, enchufes', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Ferretería General', 'description' => 'Tornillos, clavos, tuercas', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Pintura y Acabados', 'description' => 'Pinturas, brochas, rodillos', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Jardinería', 'description' => 'Herramientas de jardín, mangueras', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Seguridad Industrial', 'description' => 'Cascos, guantes, gafas', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Adhesivos y Selladores', 'description' => 'Pegamentos, silicones, cintas', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now]
        ]);

        // 2. MARCAS
        DB::table('brands')->insert([
            ['name' => 'STANLEY', 'description' => 'Herramientas profesionales de calidad superior', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'BLACK & DECKER', 'description' => 'Herramientas eléctricas para el hogar', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'DEWALT', 'description' => 'Herramientas industriales de alta resistencia', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'MAKITA', 'description' => 'Herramientas eléctricas japonesas de precisión', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'BOSCH', 'description' => 'Tecnología alemana en herramientas', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'TRUPER', 'description' => 'Herramientas mexicanas de calidad', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'MILWAUKEE', 'description' => 'Herramientas profesionales heavy duty', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'PAVCO', 'description' => 'Tuberías y accesorios de fontanería', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'LEGRAND', 'description' => 'Material eléctrico y automatización', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'COMEX', 'description' => 'Pinturas y recubrimientos', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now]
        ]);

        // 3. PROVEEDORES
        DB::table('suppliers')->insert([
            ['name' => 'Distribuidora FERREMAX S.A.C.', 'contact_name' => 'Juan Pérez', 'phone' => '01-234-5678', 'email' => 'ventas@ferremax.com', 'address' => 'Av. Industrial 1234, Lima', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'HERRAMIENTAS DEL PERÚ E.I.R.L.', 'contact_name' => 'Ana López', 'phone' => '01-345-6789', 'email' => 'pedidos@herramientasperu.com', 'address' => 'Jr. Comercio 567, Lima', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'CONSTRUMAX DISTRIBUCIONES S.A.', 'contact_name' => 'Carlos Mendoza', 'phone' => '01-456-7890', 'email' => 'compras@construmax.pe', 'address' => 'Av. Argentina 890, Callao', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'ELÉCTRICOS MODERNOS S.R.L.', 'contact_name' => 'Patricia Vargas', 'phone' => '01-567-8901', 'email' => 'contacto@electricosmodernos.com', 'address' => 'Av. Brasil 1122, Magdalena', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'PINTURAS Y ACABADOS LIMA S.A.C.', 'contact_name' => 'Roberto García', 'phone' => '01-678-9012', 'email' => 'ventas@pinturasacabados.pe', 'address' => 'Av. Colonial 1456, Pueblo Libre', 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now]
        ]);

        // 4. PRODUCTOS (selección representativa)
        $products = [
            // Herramientas Manuales (cat_id: 1)
            ['name' => 'Martillo de Uña 16 oz', 'description' => 'Martillo con mango de fibra de vidrio', 'sku' => 'MAR-STAN-16', 'price' => 45.90, 'stock' => 25, 'minimum_stock' => 5, 'category_id' => 1, 'brand_id' => 1, 'supplier_id' => 1],
            ['name' => 'Destornillador Phillips #2', 'description' => 'Destornillador punta Phillips, mango ergonómico', 'sku' => 'DES-STAN-PH2', 'price' => 12.50, 'stock' => 50, 'minimum_stock' => 10, 'category_id' => 1, 'brand_id' => 1, 'supplier_id' => 1],
            ['name' => 'Alicate Universal 8"', 'description' => 'Alicate multiuso con aislamiento eléctrico', 'sku' => 'ALI-TRUP-8', 'price' => 28.75, 'stock' => 30, 'minimum_stock' => 5, 'category_id' => 1, 'brand_id' => 6, 'supplier_id' => 2],
            
            // Herramientas Eléctricas (cat_id: 2)
            ['name' => 'Taladro Percutor 1/2" 650W', 'description' => 'Taladro con función percutora, velocidad variable', 'sku' => 'TAL-BD-650', 'price' => 189.90, 'stock' => 12, 'minimum_stock' => 3, 'category_id' => 2, 'brand_id' => 2, 'supplier_id' => 2],
            ['name' => 'Sierra Circular 7 1/4"', 'description' => 'Sierra circular 1400W con disco de 184mm', 'sku' => 'SIE-DEW-7', 'price' => 245.50, 'stock' => 8, 'minimum_stock' => 2, 'category_id' => 2, 'brand_id' => 3, 'supplier_id' => 2],
            ['name' => 'Amoladora Angular 4 1/2"', 'description' => 'Esmeriladora 750W con disco incluido', 'sku' => 'AMO-MAK-4', 'price' => 156.30, 'stock' => 15, 'minimum_stock' => 3, 'category_id' => 2, 'brand_id' => 4, 'supplier_id' => 2],
            
            // Materiales de Construcción (cat_id: 3)
            ['name' => 'Cemento Portland Tipo I - 42.5kg', 'description' => 'Cemento para construcción general', 'sku' => 'CEM-PORT-42', 'price' => 24.80, 'stock' => 100, 'minimum_stock' => 20, 'category_id' => 3, 'brand_id' => 6, 'supplier_id' => 3],
            ['name' => 'Ladrillo King Kong 18 huecos', 'description' => 'Ladrillo de arcilla cocida para muros', 'sku' => 'LAD-KK-18H', 'price' => 0.85, 'stock' => 500, 'minimum_stock' => 100, 'category_id' => 3, 'brand_id' => 6, 'supplier_id' => 3],
            ['name' => 'Fierro Corrugado 1/2" x 9m', 'description' => 'Varilla de acero para construcción', 'sku' => 'FIE-COR-12', 'price' => 28.50, 'stock' => 80, 'minimum_stock' => 15, 'category_id' => 3, 'brand_id' => 6, 'supplier_id' => 3],
            
            // Fontanería (cat_id: 4)
            ['name' => 'Tubería PVC 4" x 3m', 'description' => 'Tubo PVC para desagüe', 'sku' => 'TUB-PAV-4', 'price' => 18.90, 'stock' => 40, 'minimum_stock' => 8, 'category_id' => 4, 'brand_id' => 8, 'supplier_id' => 3],
            ['name' => 'Codo PVC 90° - 2"', 'description' => 'Codo de 90 grados para tubería', 'sku' => 'COD-PAV-2', 'price' => 3.25, 'stock' => 100, 'minimum_stock' => 20, 'category_id' => 4, 'brand_id' => 8, 'supplier_id' => 3],
            ['name' => 'Llave de Paso 1/2"', 'description' => 'Válvula esférica de bronce', 'sku' => 'LLV-TIG-12', 'price' => 15.75, 'stock' => 35, 'minimum_stock' => 8, 'category_id' => 4, 'brand_id' => 8, 'supplier_id' => 3],
            
            // Electricidad (cat_id: 5)
            ['name' => 'Cable THW 12 AWG x 100m', 'description' => 'Cable sólido para instalaciones eléctricas', 'sku' => 'CAB-LEG-12', 'price' => 85.60, 'stock' => 25, 'minimum_stock' => 5, 'category_id' => 5, 'brand_id' => 9, 'supplier_id' => 4],
            ['name' => 'Interruptor Simple', 'description' => 'Interruptor de una vía, 15A 250V', 'sku' => 'INT-LEG-SIM', 'price' => 8.90, 'stock' => 60, 'minimum_stock' => 12, 'category_id' => 5, 'brand_id' => 9, 'supplier_id' => 4],
            ['name' => 'Tomacorriente Doble', 'description' => 'Tomacorriente polarizado con tierra', 'sku' => 'TOM-LEG-DOB', 'price' => 12.50, 'stock' => 45, 'minimum_stock' => 10, 'category_id' => 5, 'brand_id' => 9, 'supplier_id' => 4],
            
            // Ferretería General (cat_id: 6)
            ['name' => 'Tornillo Autorroscante 1" x 8', 'description' => 'Tornillo punta aguda, cabeza phillips', 'sku' => 'TOR-AUT-1x8', 'price' => 0.15, 'stock' => 1000, 'minimum_stock' => 200, 'category_id' => 6, 'brand_id' => 6, 'supplier_id' => 1],
            ['name' => 'Clavo de Acero 3"', 'description' => 'Clavo común para carpintería', 'sku' => 'CLA-ACE-3', 'price' => 0.08, 'stock' => 2000, 'minimum_stock' => 400, 'category_id' => 6, 'brand_id' => 6, 'supplier_id' => 1],
            ['name' => 'Tuerca Hexagonal 1/4"', 'description' => 'Tuerca galvanizada rosca métrica', 'sku' => 'TUE-HEX-14', 'price' => 0.25, 'stock' => 500, 'minimum_stock' => 100, 'category_id' => 6, 'brand_id' => 6, 'supplier_id' => 1],
            
            // Pintura y Acabados (cat_id: 7)
            ['name' => 'Pintura Látex Blanco 4L', 'description' => 'Pintura base agua para interiores', 'sku' => 'PIN-COM-BL4', 'price' => 65.90, 'stock' => 30, 'minimum_stock' => 6, 'category_id' => 7, 'brand_id' => 10, 'supplier_id' => 5],
            ['name' => 'Brocha 3" Cerda Natural', 'description' => 'Brocha de calidad para pintura', 'sku' => 'BRO-3-CER', 'price' => 18.50, 'stock' => 25, 'minimum_stock' => 5, 'category_id' => 7, 'brand_id' => 6, 'supplier_id' => 5],
            ['name' => 'Rodillo 9" + Bandeja', 'description' => 'Kit de rodillo con bandeja plástica', 'sku' => 'ROD-9-KIT', 'price' => 22.75, 'stock' => 20, 'minimum_stock' => 4, 'category_id' => 7, 'brand_id' => 6, 'supplier_id' => 5],
            
            // Jardinería (cat_id: 8)
            ['name' => 'Manguera de Jardín 1/2" x 15m', 'description' => 'Manguera flexible con conexiones', 'sku' => 'MAN-JAR-15', 'price' => 35.80, 'stock' => 20, 'minimum_stock' => 4, 'category_id' => 8, 'brand_id' => 6, 'supplier_id' => 2],
            ['name' => 'Pala Punta Cuadrada', 'description' => 'Pala de acero con mango de madera', 'sku' => 'PAL-CUA-ACE', 'price' => 28.90, 'stock' => 15, 'minimum_stock' => 3, 'category_id' => 8, 'brand_id' => 6, 'supplier_id' => 2],
            
            // Seguridad Industrial (cat_id: 9)
            ['name' => 'Casco de Seguridad Blanco', 'description' => 'Casco HDPE con arnés ajustable', 'sku' => 'CAS-SEG-BLA', 'price' => 25.90, 'stock' => 30, 'minimum_stock' => 6, 'category_id' => 9, 'brand_id' => 6, 'supplier_id' => 2],
            ['name' => 'Guantes de Cuero', 'description' => 'Guantes de carnaza para trabajo pesado', 'sku' => 'GUA-CUE-CAR', 'price' => 15.75, 'stock' => 40, 'minimum_stock' => 8, 'category_id' => 9, 'brand_id' => 6, 'supplier_id' => 2],
            
            // Adhesivos y Selladores (cat_id: 10)
            ['name' => 'Silicona Transparente 280ml', 'description' => 'Sellador de silicona multiuso', 'sku' => 'SIL-TRA-280', 'price' => 8.50, 'stock' => 60, 'minimum_stock' => 12, 'category_id' => 10, 'brand_id' => 6, 'supplier_id' => 1],
            ['name' => 'Pegamento PVC 118ml', 'description' => 'Adhesivo para tuberías PVC', 'sku' => 'PEG-PVC-118', 'price' => 12.90, 'stock' => 45, 'minimum_stock' => 9, 'category_id' => 10, 'brand_id' => 8, 'supplier_id' => 3],
            ['name' => 'Cinta Aislante Negra', 'description' => 'Cinta eléctrica PVC 19mm x 20m', 'sku' => 'CIN-AIS-NEG', 'price' => 3.50, 'stock' => 80, 'minimum_stock' => 16, 'category_id' => 10, 'brand_id' => 9, 'supplier_id' => 4]
        ];

        foreach ($products as $product) {
            $product['is_active'] = 1;
            $product['created_at'] = $now;
            $product['updated_at'] = $now;
            DB::table('products')->insert($product);
        }

        // 5. CLIENTES
        DB::table('clients')->insert([
            ['dni' => '12345678', 'first_name' => 'Juan Carlos', 'last_name' => 'Pérez García', 'full_name' => 'Juan Carlos Pérez García', 'phone' => '987654321', 'email' => 'juan.perez@email.com', 'address' => 'Av. Los Olivos 123, San Martín de Porres', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dni' => '87654321', 'first_name' => 'María Elena', 'last_name' => 'Rodríguez López', 'full_name' => 'María Elena Rodríguez López', 'phone' => '976543210', 'email' => 'maria.rodriguez@email.com', 'address' => 'Jr. Las Flores 456, Los Olivos', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dni' => '55667788', 'first_name' => 'Carlos Alberto', 'last_name' => 'Mendoza Silva', 'full_name' => 'Carlos Alberto Mendoza Silva', 'phone' => '965432109', 'email' => 'carlos.mendoza@email.com', 'address' => 'Av. Universitaria 789, Comas', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dni' => '11223344', 'first_name' => 'Ana Sofía', 'last_name' => 'Torres Vargas', 'full_name' => 'Ana Sofía Torres Vargas', 'phone' => '954321098', 'email' => 'ana.torres@email.com', 'address' => 'Calle Real 321, Independencia', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ['dni' => '99887766', 'first_name' => 'Roberto Luis', 'last_name' => 'García Flores', 'full_name' => 'Roberto Luis García Flores', 'phone' => '943210987', 'email' => 'roberto.garcia@email.com', 'address' => 'Av. Túpac Amaru 654, Carabayllo', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now]
        ]);

        // 6. VENTAS DE EJEMPLO
        $sales = [
            ['code' => 'VTA-2024-0001', 'user_id' => 2, 'client_id' => 1, 'customer_name' => 'Juan Carlos Pérez García', 'total' => 127.15, 'status' => 'completed', 'created_at' => $now->copy()->subDays(5), 'updated_at' => $now->copy()->subDays(5)],
            ['code' => 'VTA-2024-0002', 'user_id' => 1, 'client_id' => 2, 'customer_name' => 'María Elena Rodríguez López', 'total' => 198.70, 'status' => 'completed', 'created_at' => $now->copy()->subDays(3), 'updated_at' => $now->copy()->subDays(3)],
            ['code' => 'VTA-2024-0003', 'user_id' => 2, 'client_id' => 3, 'customer_name' => 'Carlos Alberto Mendoza Silva', 'total' => 435.40, 'status' => 'completed', 'created_at' => $now->copy()->subDays(2), 'updated_at' => $now->copy()->subDays(2)]
        ];

        foreach ($sales as $sale) {
            DB::table('sales')->insert($sale);
        }

        // 7. ITEMS DE VENTAS
        $saleItems = [
            // Venta 1
            ['sale_id' => 1, 'product_id' => 1, 'quantity' => 1, 'price' => 45.90, 'created_at' => $now->copy()->subDays(5), 'updated_at' => $now->copy()->subDays(5)],
            ['sale_id' => 1, 'product_id' => 2, 'quantity' => 2, 'price' => 12.50, 'created_at' => $now->copy()->subDays(5), 'updated_at' => $now->copy()->subDays(5)],
            ['sale_id' => 1, 'product_id' => 3, 'quantity' => 1, 'price' => 28.75, 'created_at' => $now->copy()->subDays(5), 'updated_at' => $now->copy()->subDays(5)],
            ['sale_id' => 1, 'product_id' => 20, 'quantity' => 4, 'price' => 8.90, 'created_at' => $now->copy()->subDays(5), 'updated_at' => $now->copy()->subDays(5)],
            
            // Venta 2
            ['sale_id' => 2, 'product_id' => 13, 'quantity' => 1, 'price' => 85.60, 'created_at' => $now->copy()->subDays(3), 'updated_at' => $now->copy()->subDays(3)],
            ['sale_id' => 2, 'product_id' => 14, 'quantity' => 5, 'price' => 8.90, 'created_at' => $now->copy()->subDays(3), 'updated_at' => $now->copy()->subDays(3)],
            ['sale_id' => 2, 'product_id' => 15, 'quantity' => 3, 'price' => 12.50, 'created_at' => $now->copy()->subDays(3), 'updated_at' => $now->copy()->subDays(3)],
            ['sale_id' => 2, 'product_id' => 20, 'quantity' => 6, 'price' => 8.90, 'created_at' => $now->copy()->subDays(3), 'updated_at' => $now->copy()->subDays(3)],
            
            // Venta 3
            ['sale_id' => 3, 'product_id' => 4, 'quantity' => 1, 'price' => 189.90, 'created_at' => $now->copy()->subDays(2), 'updated_at' => $now->copy()->subDays(2)],
            ['sale_id' => 3, 'product_id' => 5, 'quantity' => 1, 'price' => 245.50, 'created_at' => $now->copy()->subDays(2), 'updated_at' => $now->copy()->subDays(2)]
        ];

        foreach ($saleItems as $item) {
            DB::table('sale_items')->insert($item);
        }

        echo "✅ FERRECHINCHA: Base de datos poblada exitosamente!\n";
        echo "📊 Datos creados:\n";
        echo "   - 10 Categorías\n";
        echo "   - 10 Marcas\n";
        echo "   - 5 Proveedores\n";
        echo "   - 29 Productos\n";
        echo "   - 5 Clientes\n";
        echo "   - 3 Ventas con sus items\n";
        echo "\n🔑 Credenciales de acceso:\n";
        echo "   Admin: admin@ferrechincha.com / password\n";
        echo "   Empleados: carlos@ferrechincha.com / password\n";
        echo "             maria@ferrechincha.com / password\n";
        echo "             roberto@ferrechincha.com / password\n";
    }
}
