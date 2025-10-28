<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Category;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $query = Recipe::with('category')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $recipes = $query->paginate(10)->withQueryString();
        
        // --- CAMBIO CLAVE 1 ---
        // Obtenemos TODAS las categorías (incluyendo las ocultas) para el filtro.
        $categories = Category::withTrashed()->orderBy('name')->get();

        if ($request->ajax()) {
            return view('recipes._recipe-list', compact('recipes'))->render();
        }

        return view('recipes.index', compact('recipes', 'categories'));
    }

    public function create()
    {
        // --- CAMBIO CLAVE 2 ---
        // Pasamos TODAS las categorías a la vista de creación.
        $categories = Category::withTrashed()->orderBy('name')->get();
        return view('recipes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate($this->validationRules());

        $validatedData['ingredients'] = array_filter($request->ingredients ?? []);
        $validatedData['steps'] = array_filter($request->steps ?? []);

        Recipe::create($validatedData);

        return redirect()->route('recipes.index');
    }

    public function edit(Recipe $recipe)
    {
        // --- CAMBIO CLAVE 3 ---
        // Pasamos TODAS las categorías a la vista de edición.
        $categories = Category::withTrashed()->orderBy('name')->get();
        return view('recipes.edit', compact('recipe', 'categories'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $validatedData = $request->validate($this->validationRules());

        $validatedData['ingredients'] = array_filter($request->ingredients ?? []);
        $validatedData['steps'] = array_filter($request->steps ?? []);
        
        $recipe->update($validatedData);

        return redirect()->route('recipes.index');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();
        return redirect()->route('recipes.index');
    }

    private function validationRules()
    {
        return [
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'short_description' => 'required|string|max:500',
            'long_description' => 'required|string',
            'image_url' => 'required|url',
            'ingredients' => 'nullable|array',
            'ingredients.*' => 'nullable|string',
            'steps' => 'nullable|array',
            'steps.*' => 'nullable|string',
            'difficulty' => 'required|string',
            'prep_time' => 'required|string',
            'servings' => 'required|string',
            'calories' => 'nullable|integer',
            'proteins' => 'nullable|integer',
            'fats' => 'nullable|integer',
            'carbohydrates' => 'nullable|integer',
            'fiber' => 'nullable|integer',
        ];
    }
}