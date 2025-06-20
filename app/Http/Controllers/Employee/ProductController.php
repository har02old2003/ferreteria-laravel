<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::with(['category', 'brand', 'supplier']);

            // Aplicar filtros si existen
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('brand_id')) {
                $query->where('brand_id', $request->brand_id);
            }

            if ($request->filled('supplier_id')) {
                $query->where('supplier_id', $request->supplier_id);
            }

            // Solo productos activos
            $query->where('is_active', true);

            $products = $query->latest()->paginate(15);

            // Formatear los productos para la vista
            $products->getCollection()->transform(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name ?? '',
                    'sku' => $product->sku ?? '',
                    'description' => $product->description ?? '',
                    'price' => $product->price ?? 0,
                    'stock' => $product->stock ?? 0,
                    'min_stock' => $product->min_stock ?? 0,
                    'status' => $product->status ?? 'active',
                    'category' => $product->category ? $product->category->name : 'Sin categoría',
                    'brand' => $product->brand ? $product->brand->name : 'Sin marca',
                    'supplier' => $product->supplier ? $product->supplier->name : 'Sin proveedor',
                    'created_at' => $product->created_at ? $product->created_at->format('d/m/Y H:i') : '',
                    'low_stock' => ($product->stock ?? 0) <= ($product->min_stock ?? 0),
                ];
            });

            // Obtener datos para filtros
            $categories = Category::orderBy('name')->get(['id', 'name']);
            $brands = Brand::orderBy('name')->get(['id', 'name']);
            $suppliers = Supplier::orderBy('name')->get(['id', 'name']);

            return Inertia::render('Employee/Products/Index', [
                'products' => $products,
                'categories' => $categories ?? [],
                'brands' => $brands ?? [],
                'suppliers' => $suppliers ?? [],
                'filters' => $request->only(['search', 'category_id', 'brand_id', 'supplier_id']) ?? []
            ]);

        } catch (\Exception $e) {
            Log::error('Error en Employee ProductController index: ' . $e->getMessage());
            
            return Inertia::render('Employee/Products/Index', [
                'products' => ['data' => [], 'links' => [], 'meta' => []],
                'categories' => [],
                'brands' => [],
                'suppliers' => [],
                'filters' => [],
                'error' => 'Error al cargar los productos'
            ]);
        }
    }

    public function show(Product $product)
    {
        try {
            $product->load(['category', 'brand', 'supplier']);

            $productData = [
                'id' => $product->id,
                'name' => $product->name ?? '',
                'sku' => $product->sku ?? '',
                'description' => $product->description ?? '',
                'price' => $product->price ?? 0,
                'stock' => $product->stock ?? 0,
                'min_stock' => $product->min_stock ?? 0,
                'status' => $product->status ?? 'active',
                'category' => $product->category ? $product->category->name : 'Sin categoría',
                'brand' => $product->brand ? $product->brand->name : 'Sin marca',
                'supplier' => $product->supplier ? $product->supplier->name : 'Sin proveedor',
                'created_at' => $product->created_at ? $product->created_at->format('d/m/Y H:i') : '',
                'updated_at' => $product->updated_at ? $product->updated_at->format('d/m/Y H:i') : '',
                'low_stock' => ($product->stock ?? 0) <= ($product->min_stock ?? 0),
            ];

            return Inertia::render('Employee/Products/Show', [
                'product' => $productData
            ]);

        } catch (\Exception $e) {
            Log::error('Error en Employee ProductController show: ' . $e->getMessage());
            
            return redirect()->route('employee.products.index')
                ->with('error', 'Error al cargar el producto');
        }
    }
} 