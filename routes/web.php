<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\RecipeController;



Route::get('/', function () {
    return redirect()->route('recipes.index');
});

// Rutas para administrar categorías
Route::resource('categories', CategoryController::class);

// Rutas para administrar recetas
Route::resource('recipes', RecipeController::class);


Route::post('categories/{id}/restore', [CategoryController::class, 'restore'])->name('categories.restore');