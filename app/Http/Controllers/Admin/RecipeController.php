<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

    public function generateAi(Request $request)
    {
        $request->validate(['prompt' => 'required|string|max:255']);
        $promptUser = $request->input('prompt');
        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            return response()->json(['error' => 'Falta tu API Key en el .env'], 500);
        }

$systemPrompt = "Eres un chef. Crea una receta para: '$promptUser'. Devuelve SOLO un JSON válido con las llaves exactas en inglés: name, short_description, long_description, ingredients (array de strings), steps (array de strings), difficulty, prep_time, servings, calories (SOLO NUMEROS ENTEROS, sin 'kcal'), proteins (SOLO NUMEROS ENTEROS, sin 'g'), fats (SOLO NUMEROS ENTEROS), carbohydrates (SOLO NUMEROS ENTEROS), fiber (SOLO NUMEROS ENTEROS).";
        try {
            $response = Http::timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key={$apiKey}", [
                'contents' => [['parts' => [['text' => $systemPrompt]]]],
                'generationConfig' => ['response_mime_type' => 'application/json']
            ]);

            $data = $response->json();

            if (isset($data['error'])) throw new \Exception($data['error']['message']);

            $recipeData = json_decode($data['candidates'][0]['content']['parts'][0]['text'], true);

            return response()->json(['success' => true, 'recipe' => $recipeData]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error con la IA: ' . $e->getMessage()], 500);
        }
    }
}
