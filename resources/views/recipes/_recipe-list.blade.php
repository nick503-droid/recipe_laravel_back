<tbody class="bg-white divide-y divide-gray-200" id="recipe-table-body">
    @forelse ($recipes as $recipe)
        <tr>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-20 w-20">
                        <img class="h-20 w-20 rounded-md object-cover" src="{{ $recipe->image_url }}" alt="{{ $recipe->name }}">
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ $recipe->name }}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ $recipe->category->name }}
                @if ($recipe->category->trashed())
                    <span class="text-red-500 font-semibold text-xs">(Oculta)</span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $recipe->difficulty }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end items-center space-x-3">
                    <a href="{{ route('recipes.edit', $recipe->id) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                    <form action="{{ route('recipes.destroy', $recipe->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de eliminar esta receta?')">Eliminar</button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
         <tr>
            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No se encontraron recetas con los filtros aplicados.</td>
        </tr>
    @endforelse
</tbody>