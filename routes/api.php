<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\ChefController;

// Endpoint para obtener todas las categorías
Route::get('/categories', [CategoryController::class, 'index']);

// Endpoint para obtener una categoría específica y sus recetas
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Endpoint para obtener todas las recetas
Route::get('/recipes', [RecipeController::class, 'index']);

// Endpoint para obtener una receta específica
Route::get('/recipes/{recipe}', [RecipeController::class, 'show']);

Route::post('/chef-ia', [ChefController::class, 'handle']);

// routes/api.php

Route::get('/check-models', [App\Http\Controllers\Api\ChefController::class, 'checkModels']);