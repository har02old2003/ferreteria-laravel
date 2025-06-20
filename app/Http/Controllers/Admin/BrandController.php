<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Brands/Index', [
            'brands' => Brand::query()
                ->withCount('products')
                ->when($request->input('search'), function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('id', 'like', "%{$search}%");
                    });
                })
                ->latest()
                ->get()
                ->map(function ($brand) {
                    return [
                        'id' => $brand->id,
                        'formatted_id' => 'M' . str_pad($brand->id, 4, '0', STR_PAD_LEFT),
                        'name' => $brand->name,
                        'description' => $brand->description,
                        'is_active' => $brand->is_active,
                        'products_count' => (int) $brand->products_count,
                    ];
                }),
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean'
        ]);

        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('message', 'Marca creada exitosamente')
            ->with('type', 'success');
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'description' => 'nullable|string',
            'is_active' => 'required|boolean'
        ]);

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('message', 'Marca actualizada exitosamente')
            ->with('type', 'success');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->back()
            ->with('message', 'Marca eliminada exitosamente')
            ->with('type', 'error');
    }

    public function toggleStatus(Brand $brand)
    {
        $brand->update([
            'is_active' => !$brand->is_active
        ]);

        return redirect()->back()
            ->with('message', 'Estado de la marca actualizado exitosamente')
            ->with('type', 'success');
    }
} 