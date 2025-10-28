@extends('layouts.app')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestión de Recetas</h1>
        <a href="{{ route('recipes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Crear Receta</a>
    </div>

    <div class="mb-4 bg-white p-4 rounded-lg shadow-md">
        <form action="{{ route('recipes.index') }}" method="GET" id="filter-form" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700">Filtrar por Categoría</label>
                
                <select name="category_id" id="category_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Todas las categorías</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} @if($category->trashed()) (Oculta) @endif
                        </option>
                    @endforeach
                </select>


            </div>
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Buscar por nombre</label>
                <input type="text" name="search" id="search-input" placeholder="Escribe para buscar..." value="{{ request('search') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
            </div>
            <div>
                 <a href="{{ route('recipes.index') }}" class="w-full text-center bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">Limpiar Filtros</a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
             <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dificultad</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            @include('recipes._recipe-list', ['recipes' => $recipes])
        </table>
    </div>
    <div class="mt-6" id="pagination-links">
        {{ $recipes->links() }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const categorySelect = document.getElementById('category_id');
            const tableContainer = document.querySelector('.min-w-full'); // Apuntamos a la tabla
            let debounceTimer;

            function fetchRecipes() {
                const form = document.getElementById('filter-form');
                const formData = new FormData(form);
                const params = new URLSearchParams(formData).toString();
                const url = `{{ route('recipes.index') }}?${params}`;

                // Muestra un indicador de carga (opcional pero recomendado)
                tableContainer.style.opacity = '0.5';

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    // El HTML que recibimos es solo el tbody, así que reemplazamos el tbody existente
                    const currentTbody = document.getElementById('recipe-table-body');
                    if (currentTbody) {
                       currentTbody.outerHTML = html;
                    }
                    tableContainer.style.opacity = '1'; // Restaura la opacidad
                })
                .catch(error => {
                    console.error('Error al filtrar:', error);
                    tableContainer.style.opacity = '1'; // Restaura opacidad en caso de error
                });
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                // Esto es un "debounce": espera 300ms después de que dejas de teclear para buscar.
                // Evita hacer una petición al servidor por cada letra que escribes.
                debounceTimer = setTimeout(fetchRecipes, 300);
            });

            categorySelect.addEventListener('change', fetchRecipes);
        });
    </script>

@endsection