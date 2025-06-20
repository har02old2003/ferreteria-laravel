<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Models\Product;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener usuarios y productos existentes
        $users = User::all();
        $products = Product::all();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->info('No hay usuarios o productos para crear ventas');
            return;
        }

        // Crear 20 ventas de ejemplo
        for ($i = 0; $i < 20; $i++) {
            $user = $users->random();
            $total = 0;

            $sale = Sale::create([
                'user_id' => $user->id,
                'status' => collect(['completed', 'pending', 'cancelled'])->random(),
                'payment_method' => collect(['cash', 'card', 'transfer'])->random(),
                'notes' => 'Venta de ejemplo #' . ($i + 1),
                'total' => 0 // Se calculará después
            ]);

            // Agregar entre 1 y 5 productos a cada venta
            $numItems = rand(1, 5);
            for ($j = 0; $j < $numItems; $j++) {
                $product = $products->random();
                $quantity = rand(1, 3);
                $price = $product->price;
                $subtotal = $quantity * $price;
                $total += $subtotal;

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price
                ]);
            }

            // Actualizar el total de la venta
            $sale->update(['total' => $total]);
        }

        $this->command->info('Se crearon 20 ventas de ejemplo con sus items');
    }
}
