<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Muestra la lista de categorías
     */
    public function index(Request $request)
    {
        return Inertia::render('Admin/Categories/Index', [
            'categories' => Category::query()
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
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'formatted_id' => 'C' . str_pad($category->id, 4, '0', STR_PAD_LEFT),
                        'name' => $category->name,
                        'description' => $category->description,
                        'is_active' => $category->is_active,
                        'products_count' => $category->products_count,
                    ];
                }),
            'filters' => $request->only(['search'])
        ]);
    }

    /**
     * Almacena una nueva categoría
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Category::create($validated);

        return redirect()->back()
            ->with('message', 'Categoría creada exitosamente')
            ->with('type', 'success');
    }

    /**
     * Actualiza una categoría existente
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $category->update($validated);

        return redirect()->back()
            ->with('message', 'Categoría actualizada exitosamente')
            ->with('type', 'success');
    }

    public function toggleStatus(Category $category)
    {
        $category->update([
            'is_active' => !$category->is_active
        ]);

        return redirect()->back()
            ->with('message', 'Estado de la categoría actualizado exitosamente')
            ->with('type', 'success');
    }

    /**
     * Elimina una categoría
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->back()
            ->with('message', 'Categoría eliminada exitosamente')
            ->with('type', 'error');
    }
} 