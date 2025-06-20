<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function index()
    {
        try {
            $sales = Sale::with(['products', 'items.product', 'user', 'client'])
                ->latest()
                ->paginate(15)
                ->through(function ($sale) {
                    // Usar items si existen, sino usar products con pivot
                    $products = collect([]);
                    if ($sale->items && $sale->items->count() > 0) {
                        $products = $sale->items->map(function ($item) {
                            return [
                                'id' => $item->product ? $item->product->id : 0,
                                'name' => $item->product ? $item->product->name : 'Producto eliminado',
                                'quantity' => (int) ($item->quantity ?? 0),
                                'price' => (float) ($item->price ?? 0),
                                'subtotal' => (float) (($item->quantity ?? 0) * ($item->price ?? 0))
                            ];
                        });
                    } elseif ($sale->products && $sale->products->count() > 0) {
                        $products = $sale->products->map(function ($product) {
                            return [
                                'id' => $product->id ?? 0,
                                'name' => $product->name ?? 'Producto sin nombre',
                                'quantity' => (int) ($product->pivot->quantity ?? 0),
                                'price' => (float) ($product->pivot->price ?? 0),
                                'subtotal' => (float) (($product->pivot->quantity ?? 0) * ($product->pivot->price ?? 0))
                            ];
                        });
                    }

                    // Extraer el valor numérico del total si viene como string con formato
                    $totalValue = $sale->total ?? 0;
                    if (is_string($totalValue)) {
                        // Remover "S/ " y convertir a float
                        $totalValue = (float) str_replace(['S/ ', ','], '', $totalValue);
                    }

                    return [
                        'id' => $sale->id ?? 0,
                        'code' => $sale->code ?? ('V' . str_pad($sale->id ?? 0, 4, '0', STR_PAD_LEFT)),
                        'total' => (float) $totalValue,
                        'status' => $sale->status ?? 'pending',
                        'customer_name' => $sale->customer_name ?? null,
                        'created_at' => $sale->created_at ? $sale->created_at->toISOString() : Carbon::now()->toISOString(),
                        'user' => $sale->user ? [
                            'id' => $sale->user->id,
                            'name' => $sale->user->name
                        ] : null,
                        'client' => $sale->client ? [
                            'id' => $sale->client->id,
                            'full_name' => $sale->client->full_name ?? 'Cliente sin nombre',
                            'dni' => $sale->client->dni ?? ''
                        ] : null,
                        'products' => $products->toArray()
                    ];
                });

            return Inertia::render('Employee/Sales/Index', [
                'sales' => $sales
            ]);
        } catch (\Exception $e) {
            Log::error('Error in Employee SaleController index: ' . $e->getMessage());
            
            return Inertia::render('Employee/Sales/Index', [
                'sales' => [
                    'data' => [],
                    'links' => [],
                    'meta' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'per_page' => 15,
                        'total' => 0
                    ]
                ],
                'error' => 'Hubo un error al cargar las ventas'
            ]);
        }
    }

    public function show(Sale $sale)
    {
        try {
            $sale->load(['products', 'items.product', 'user', 'client']);
            
            // Usar items si existen, sino usar products con pivot
            $products = collect([]);
            if ($sale->items && $sale->items->count() > 0) {
                $products = $sale->items->map(function ($item) {
                    return [
                        'id' => $item->product ? $item->product->id : 0,
                        'name' => $item->product ? $item->product->name : 'Producto eliminado',
                        'quantity' => (int) ($item->quantity ?? 0),
                        'price' => (float) ($item->price ?? 0),
                        'subtotal' => (float) (($item->quantity ?? 0) * ($item->price ?? 0))
                    ];
                });
            } elseif ($sale->products && $sale->products->count() > 0) {
                $products = $sale->products->map(function ($product) {
                    return [
                        'id' => $product->id ?? 0,
                        'name' => $product->name ?? 'Producto sin nombre',
                        'quantity' => (int) ($product->pivot->quantity ?? 0),
                        'price' => (float) ($product->pivot->price ?? 0),
                        'subtotal' => (float) (($product->pivot->quantity ?? 0) * ($product->pivot->price ?? 0))
                    ];
                });
            }
            
            $saleData = [
                'id' => $sale->id ?? 0,
                'code' => $sale->code ?? ('V' . str_pad($sale->id ?? 0, 4, '0', STR_PAD_LEFT)),
                'total' => (float) ($sale->total ?? 0),
                'status' => $sale->status ?? 'pending',
                'customer_name' => $sale->customer_name ?? null,
                'created_at' => $sale->created_at ? $sale->created_at->toISOString() : Carbon::now()->toISOString(),
                'user' => $sale->user ? [
                    'id' => $sale->user->id,
                    'name' => $sale->user->name
                ] : null,
                'client' => $sale->client ? [
                    'id' => $sale->client->id,
                    'full_name' => $sale->client->full_name ?? 'Cliente sin nombre',
                    'dni' => $sale->client->dni ?? ''
                ] : null,
                'products' => $products->toArray()
            ];

            // Si es una petición AJAX, retornar JSON
            if (request()->wantsJson()) {
                return response()->json([
                    'props' => [
                        'sale' => $saleData
                    ]
                ]);
            }
            
            // Si no es AJAX, retornar vista de Inertia (para compatibilidad)
            return Inertia::render('Employee/Sales/Show', [
                'sale' => $saleData
            ]);
        } catch (\Exception $e) {
            Log::error('Error in Employee SaleController show: ' . $e->getMessage());
            
            if (request()->wantsJson()) {
                return response()->json(['error' => 'No se pudo cargar la venta solicitada'], 500);
            }
            
            return redirect()->route('employee.sales.index')
                ->with('error', 'No se pudo cargar la venta solicitada');
        }
    }

    public function create()
    {
        try {
            $products = Product::where('is_active', true)
                ->where('stock', '>', 0)
                ->with(['category', 'brand', 'supplier'])
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => $product->id ?? 0,
                        'sku' => $product->sku ?? '',
                        'name' => $product->name ?? 'Producto sin nombre',
                        'description' => $product->description ?? '',
                        'price' => (float) ($product->price ?? 0),
                        'stock' => (int) ($product->stock ?? 0),
                        'category_id' => $product->category_id ?? null,
                        'brand_id' => $product->brand_id ?? null,
                        'supplier_id' => $product->supplier_id ?? null,
                        'category' => $product->category ? $product->category->name : null,
                        'brand' => $product->brand ? $product->brand->name : null,
                        'supplier' => $product->supplier ? $product->supplier->name : null,
                        'image' => $product->image ?? null,
                        'status' => ($product->is_active ?? false) ? 'active' : 'inactive',
                    ];
                });

            $categories = \App\Models\Category::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name ?? 'Categoría sin nombre'
                    ];
                });

            $brands = \App\Models\Brand::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(function ($brand) {
                    return [
                        'id' => $brand->id,
                        'name' => $brand->name ?? 'Marca sin nombre'
                    ];
                });

            $suppliers = \App\Models\Supplier::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(function ($supplier) {
                    return [
                        'id' => $supplier->id,
                        'name' => $supplier->name ?? 'Proveedor sin nombre'
                    ];
                });

            if (request()->wantsJson()) {
                return response()->json([
                    'props' => [
                        'products' => $products->values()->toArray(),
                        'categories' => $categories->values()->toArray(),
                        'brands' => $brands->values()->toArray(),
                        'suppliers' => $suppliers->values()->toArray(),
                    ]
                ]);
            }

            return Inertia::render('Employee/Sales/Create', [
                'products' => $products->values()->toArray(),
                'categories' => $categories->values()->toArray(),
                'brands' => $brands->values()->toArray(),
                'suppliers' => $suppliers->values()->toArray(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in Employee SaleController create: ' . $e->getMessage());
            
            return Inertia::render('Employee/Sales/Create', [
                'products' => [],
                'categories' => [],
                'brands' => [],
                'suppliers' => [],
                'error' => 'Hubo un error al cargar los datos para crear la venta'
            ]);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'customer_name' => 'nullable|string|max:255',
            'total' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Verificar stock disponible
            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception("Producto no encontrado");
                }
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stock insuficiente para el producto: {$product->name}");
                }
            }

            $sale = Sale::create([
                'user_id' => auth()->id(),
                'customer_name' => $validated['customer_name'] ?? null,
                'total' => $validated['total'],
                'status' => 'completed',
                'code' => 'V' . str_pad(Sale::count() + 1, 6, '0', STR_PAD_LEFT),
            ]);

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                
                // Crear en tabla pivot (para compatibilidad)
                $sale->products()->attach($product->id, [
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);

                // Crear en tabla sale_items (nuevo sistema)
                \App\Models\SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);

                // Actualizar stock
                $product->decrement('stock', $item['quantity']);
            }

            DB::commit();

            return redirect()->route('employee.sales.index')
                ->with('success', 'Venta creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating sale: ' . $e->getMessage());
            
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function ticket(Sale $sale)
    {
        try {
            $sale->load(['products', 'items.product', 'user', 'client']);
            
            // Usar items si existen, sino usar products con pivot
            $products = collect([]);
            if ($sale->items && $sale->items->count() > 0) {
                $products = $sale->items->map(function ($item) {
                    return [
                        'name' => $item->product ? $item->product->name : 'Producto eliminado',
                        'quantity' => (int) ($item->quantity ?? 0),
                        'price' => (float) ($item->price ?? 0),
                        'subtotal' => (float) (($item->quantity ?? 0) * ($item->price ?? 0))
                    ];
                });
            } elseif ($sale->products && $sale->products->count() > 0) {
                $products = $sale->products->map(function ($product) {
                    return [
                        'name' => $product->name ?? 'Producto sin nombre',
                        'quantity' => (int) ($product->pivot->quantity ?? 0),
                        'price' => (float) ($product->pivot->price ?? 0),
                        'subtotal' => (float) (($product->pivot->quantity ?? 0) * ($product->pivot->price ?? 0))
                    ];
                });
            }

            return view('tickets.sale', [
                'sale' => [
                    'code' => $sale->code ?? ('V' . str_pad($sale->id ?? 0, 4, '0', STR_PAD_LEFT)),
                    'total' => (float) ($sale->total ?? 0),
                    'customer_name' => $sale->customer_name ?? 'Cliente General',
                    'created_at' => $sale->created_at ? $sale->created_at->format('d/m/Y H:i') : date('d/m/Y H:i'),
                    'user' => $sale->user ? $sale->user->name : 'Usuario desconocido',
                    'products' => $products->toArray()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error generating ticket: ' . $e->getMessage());
            
            return response()->json(['error' => 'No se pudo generar el ticket'], 500);
        }
    }
} 