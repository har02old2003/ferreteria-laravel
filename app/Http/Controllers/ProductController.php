<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }

    public function index()
    {
        $products = Product::with(['category', 'brand', 'supplier'])->paginate(10);

        // Mapear para asegurar que price sea float
        $products->getCollection()->transform(function ($product) {
            $product->price = $product->price !== null ? (float) $product->price : 0.0;
            return $product;
        });

        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'suppliers' => $suppliers
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit' => 'required|string|in:unidad,kg,litro,metro,lote,caja,paquete'
        ]);

        Product::create($request->all());

        return redirect()->back()->with('success', 'Producto creado exitosamente.');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'unit' => 'required|string|in:unidad,kg,litro,metro,lote,caja,paquete'
        ]);

        $product->update($request->all());

        return redirect()->back()->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Producto eliminado exitosamente.');
    }
} 