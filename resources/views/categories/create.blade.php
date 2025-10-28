@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Crear Nueva Categoría</h1>

    <div class="bg-white p-8 rounded-lg shadow-md">
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre de la Categoría</label>
                <input type="text" name="name" id="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="mb-6">
                <label for="image_url" class="block text-sm font-medium text-gray-700">URL de la Imagen</label>
                <input type="url" name="image_url" id="image_url_input" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700">Descripción de la Categoría</label>
                <textarea name="description" id="description" rows="3" class="mt-1 block w-full input-style">{{ old('description') }}</textarea>
                <p class="mt-2 text-sm text-gray-500">Esta descripción aparecerá en el encabezado de la página de la colección.</p>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700">Previsualización</label>
                <img id="image_preview" src="https://via.placeholder.com/300x200.png?text=Previsualización" alt="Previsualización de la imagen" class="mt-2 w-full max-w-sm h-auto rounded-md object-cover border border-gray-200">
            </div>

            <div class="flex items-center justify-end">
                <a href="{{ route('categories.index') }}" class="text-gray-600 mr-4">Cancelar</a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Guardar Categoría</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const imageUrlInput = document.getElementById('image_url_input');
            const imagePreview = document.getElementById('image_preview');
            const placeholder = 'https://via.placeholder.com/300x200.png?text=Previsualización';

            imageUrlInput.addEventListener('input', function() {
                // Si hay una URL válida, la usamos; si no, ponemos el placeholder
                imagePreview.src = this.value || placeholder;
            });
            
            // Si la URL de la imagen es inválida, vuelve al placeholder
            imagePreview.onerror = function() {
                this.src = placeholder;
            };
        });
    </script>
@endsection