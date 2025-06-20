<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['category', 'brand', 'supplier']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('brand', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Simplificado para debug
        $products = $query->latest()->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'formatted_id' => 'PR' . str_pad($product->id, 4, '0', STR_PAD_LEFT),
                'name' => $product->name ?? 'Sin nombre',
                'description' => $product->description ?? '',
                'category_id' => $product->category_id,
                'category' => $product->category?->name ?? 'Sin categoría',
                'brand_id' => $product->brand_id,
                'brand' => $product->brand?->name ?? 'Sin marca',
                'supplier_id' => $product->supplier_id,
                'supplier' => $product->supplier?->name ?? 'Sin proveedor',
                'sku' => $product->sku ?? 'Sin SKU',
                'price' => (float) ($product->price ?? 0),
                'stock' => (int) ($product->stock ?? 0),
                'min_stock' => (int) ($product->minimum_stock ?? 0),
                'image_url' => $product->image ? Storage::url($product->image) : null,
                'has_expiration' => (bool) ($product->has_expiration ?? false),
                'expiration_date' => $product->expiration_date ? $product->expiration_date->format('Y-m-d') : null,
                'is_active' => (bool) $product->is_active,
            ];
        });

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => Category::where('is_active', true)->get(),
            'brands' => Brand::where('is_active', true)->get(),
            'suppliers' => Supplier::where('is_active', true)->get(),
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'sku' => 'required|string|max:50|unique:products',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'has_expiration' => 'required|boolean',
            'expiration_date' => 'nullable|date|after_or_equal:today',
            'is_active' => 'required|boolean',
        ]);

        // Establecer stock mínimo automático en 5
        $validated['minimum_stock'] = 5;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Si no tiene caducidad, limpiar la fecha
        if (!$validated['has_expiration']) {
            $validated['expiration_date'] = null;
        }

        Product::create($validated);

        return redirect()->back()
            ->with('message', 'Producto creado exitosamente')
            ->with('type', 'success');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load('category');
        
        return Inertia::render('Admin/Products/Show', [
            'product' => [
                'id' => $product->id,
                'formatted_code' => $product->formatted_code,
                'name' => $product->name,
                'description' => $product->description,
                'price' => (float) $product->price,
                'stock' => (int) $product->stock,
                'category' => $product->category,
                'brand' => $product->brand,
                'unit' => $product->unit,
                'has_expiration' => $product->has_expiration,
                'expiration_date' => $product->expiration_date,
                'expiration_status' => $product->expiration_status,
                'stock_status' => $product->stock_status,
                'location' => $product->location,
                'minimum_stock' => (float) $product->minimum_stock,
                'supplier' => $product->supplier,
                'image_url' => $product->image_path ? Storage::url($product->image_path) : null,
                'is_active' => $product->is_active,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ]
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
            'has_expiration' => 'required|boolean',
            'expiration_date' => 'nullable|date|after_or_equal:today',
            'is_active' => 'required|boolean',
        ]);

        // Establecer stock mínimo automático en 5
        $validated['minimum_stock'] = 5;

        if ($request->hasFile('image')) {
            // Eliminar la imagen anterior si existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        // Si no tiene caducidad, limpiar la fecha
        if (!$validated['has_expiration']) {
            $validated['expiration_date'] = null;
        }

        $product->update($validated);

        return redirect()->back()
            ->with('message', 'Producto actualizado exitosamente')
            ->with('type', 'success');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return redirect()->back()
            ->with('message', 'Producto eliminado exitosamente')
            ->with('type', 'error');
    }

    public function toggleStatus(Product $product)
    {
        $product->update([
            'is_active' => !$product->is_active
        ]);

        return redirect()->back()
            ->with('message', 'Estado del producto actualizado exitosamente')
            ->with('type', 'success');
    }
}
