<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['products', 'items.product', 'user', 'client'])
            ->latest()
            ->get()
            ->map(function ($sale) {
                // Usar items si existen, sino usar products con pivot
                $products = collect([]);
                
                if ($sale->items->count() > 0) {
                    $products = $sale->items->filter(function ($item) {
                        return $item->product !== null; // Filtrar items sin producto
                    })->map(function ($item) {
                        return [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'quantity' => (int) $item->quantity,
                            'price' => (float) $item->price,
                            'subtotal' => (float) ($item->quantity * $item->price)
                        ];
                    });
                } elseif ($sale->products->count() > 0) {
                    $products = $sale->products->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'quantity' => (int) $product->pivot->quantity,
                            'price' => (float) $product->pivot->price,
                            'subtotal' => (float) ($product->pivot->quantity * $product->pivot->price)
                        ];
                    });
                }

                return [
                    'id' => $sale->id,
                    'code' => $sale->code ?? 'V' . str_pad($sale->id, 4, '0', STR_PAD_LEFT),
                    'total' => 'S/ ' . number_format($sale->total ?? 0, 2),
                    'status' => $sale->status, // Status original para comparaciones
                    'status_label' => $sale->status === 'completed' ? 'completada' : ($sale->status === 'pending' ? 'pendiente' : 'cancelada'), // Status traducido para mostrar
                    'created_at' => $sale->created_at?->format('d/m/Y H:i'),
                    'user' => $sale->user ? [
                        'id' => $sale->user->id,
                        'name' => $sale->user->name
                    ] : null,
                    'client' => $sale->client ? [
                        'id' => $sale->client->id,
                        'full_name' => $sale->client->full_name,
                        'dni' => $sale->client->dni
                    ] : null,
                    'products' => $products
                ];
            });

        $products = Product::active()->get();

        return Inertia::render('Admin/Sales/Index', [
            'sales' => $sales
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'client_id' => 'nullable|exists:clients,id',
            'client_data' => 'nullable|array',
            'client_data.dni' => 'required_with:client_data|string|size:8',
            'client_data.first_name' => 'required_with:client_data|string|max:255',
            'client_data.last_name' => 'required_with:client_data|string|max:255',
            'client_data.phone' => 'nullable|string|max:20',
            'client_data.email' => 'nullable|email|max:255',
            'client_data.address' => 'nullable|string|max:500',
        ]);

        $clientId = null;

        // Manejar cliente si se proporcionó
        if ($request->has('client_data') && $request->client_data) {
            if ($request->client_id) {
                // Cliente existente
                $clientId = $request->client_id;
            } else {
                // Crear nuevo cliente
                $client = Client::create([
                    'dni' => $request->client_data['dni'],
                    'first_name' => $request->client_data['first_name'],
                    'last_name' => $request->client_data['last_name'],
                    'full_name' => $request->client_data['full_name'],
                    'phone' => $request->client_data['phone'] ?? null,
                    'email' => $request->client_data['email'] ?? null,
                    'address' => $request->client_data['address'] ?? null,
                    'status' => 'active'
                ]);
                $clientId = $client->id;
            }
        }

        $sale = Sale::create([
            'user_id' => auth()->id(),
            'client_id' => $clientId,
            'total' => 0,
            'status' => 'completed'
        ]);

        $total = 0;
        foreach ($validated['products'] as $item) {
            $product = Product::find($item['id']);
            $price = $product->price;
            $subtotal = $price * $item['quantity'];
            $total += $subtotal;

            // Crear en tabla pivot (para compatibilidad)
            $sale->products()->attach($product->id, [
                'quantity' => $item['quantity'],
                'price' => $price
            ]);

            // Crear en tabla sale_items (nuevo sistema)
            \App\Models\SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $price
            ]);

            // Actualizar stock
            $product->update([
                'stock' => $product->stock - $item['quantity']
            ]);
        }

        $sale->update(['total' => $total]);

        // Generar código de venta si no existe
        if (!$sale->code) {
            $sale->update(['code' => 'V' . str_pad($sale->id, 6, '0', STR_PAD_LEFT)]);
        }

        // Redirigir de vuelta al índice de ventas
        return redirect()->route('admin.sales.index')
            ->with('message', 'Venta registrada exitosamente')
            ->with('type', 'success');
    }

    public function show(Sale $sale)
    {
        $sale->load(['products', 'user']);
        
        return Inertia::render('Admin/Sales/Show', [
            'sale' => [
                'id' => $sale->id,
                'code' => $sale->code,
                'total' => $sale->total,
                'status' => $sale->status,
                'created_at' => $sale->created_at,
                'user' => $sale->user,
                'products' => $sale->products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'quantity' => $product->pivot->quantity,
                        'price' => $product->pivot->price,
                        'subtotal' => $product->pivot->quantity * $product->pivot->price
                    ];
                })
            ]
        ]);
    }

    public function ticket(Sale $sale)
    {
        $sale->load(['products', 'user']);
        
        return view('admin.sales.ticket', [
            'sale' => $sale,
            'company' => [
                'name' => config('app.name'),
                'address' => 'Dirección de la empresa',
                'phone' => 'Teléfono de la empresa',
                'email' => 'Email de la empresa'
            ]
        ]);
    }

    public function create()
    {
        $products = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->with(['category', 'brand', 'supplier'])
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'sku' => $product->sku,
                    'name' => $product->name,
                    'price' => (float) $product->price,
                    'stock' => (int) $product->stock,
                    'category_id' => $product->category_id,
                    'brand_id' => $product->brand_id,
                    'supplier_id' => $product->supplier_id,
                    'category' => $product->category ? $product->category->name : null,
                    'brand' => $product->brand ? $product->brand->name : null,
                    'supplier' => $product->supplier ? $product->supplier->name : null,
                ];
            });

        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();

        if (request()->wantsJson()) {
            return response()->json([
                'props' => [
                    'products' => $products,
                    'categories' => $categories,
                    'brands' => $brands,
                    'suppliers' => $suppliers,
                ]
            ]);
        }

        return Inertia::render('Admin/Sales/Create', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'suppliers' => $suppliers,
        ]);
    }

    public function destroy(Sale $sale)
    {
        try {
            // Restaurar stock de productos
            foreach ($sale->products as $product) {
                $product->update([
                    'stock' => $product->stock + $product->pivot->quantity
                ]);
            }

            // Eliminar la venta
            $sale->delete();

            return redirect()->route('admin.sales.index')
                ->with('message', 'Venta eliminada correctamente')
                ->with('type', 'error');
        } catch (\Exception $e) {
            return redirect()->route('admin.sales.index')
                ->with('message', 'Error al eliminar la venta')
                ->with('type', 'error');
        }
    }
}
