<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\RecipeResource;


class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // La API pública solo muestra categorías activas (no las ocultas)
        $categories = Category::orderBy('name')->get();
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    // app/Http/Controllers/Api/CategoryController.php
    public function show(Category $category)
    {
    // Cargamos las recetas de la categoría de forma paginada
    $recipes = $category->recipes()->paginate(10);

    // Devolvemos un JSON que contiene tanto los datos de la categoría como sus recetas
    return response()->json([
        'category' => new CategoryResource($category),
        'recipes' => RecipeResource::collection($recipes)
    ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
