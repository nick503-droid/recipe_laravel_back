<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        // Obtenemos todas las categorías, incluyendo las "ocultas" (soft deleted)
        $categories = Category::withTrashed()->latest()->get();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'image_url' => 'required|url',
            'description' => 'nullable|string',
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index');
    }

    // NUEVO: Muestra el formulario de edición
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    // NUEVO: Actualiza la categoría en la base de datos
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'image_url' => 'required|url',
            'description' => 'nullable|string',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index');
    }

    public function destroy(Category $category)
    {
        // Ahora esto hará un Soft Delete
        $category->delete();
        return redirect()->route('categories.index');
    }

    // NUEVO: Restaura una categoría oculta
    public function restore($id)
    {
        Category::withTrashed()->find($id)->restore();
        return redirect()->route('categories.index');
    }
}