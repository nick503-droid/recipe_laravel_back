@csrf

@if ($errors->any())
    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-md" role="alert">
        <p class="font-bold">¡Ups! Hubo algunos problemas con tu entrada.</p>
        <ul class="mt-3 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-8 bg-gradient-to-r from-indigo-50 to-blue-50 p-6 rounded-lg shadow-sm border border-indigo-100">
    <h3 class="text-lg font-bold text-indigo-800 mb-2 flex items-center gap-2">
        🪄 Generador Mágico con IA
    </h3>
    <div class="flex flex-col sm:flex-row gap-4 items-start">
        <div class="flex-1 w-full">
            <input type="text" id="ai_prompt" placeholder="Ej: Una receta salvadoreña de pupusas saludables..." class="block w-full input-style border-indigo-300 focus:border-indigo-500">
            <p class="text-xs text-indigo-600 mt-1">Escribe lo que deseas y la IA llenará todo el formulario por ti.</p>
        </div>
        <button type="button" id="btn_generate_ai" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-md shadow transition-colors w-full sm:w-auto">
            ¡Generar Receta! ✨
        </button>
    </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <div class="md:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium mb-4 border-b pb-2">Información Principal</h3>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nombre de la Receta</label>
                <input type="text" name="name" id="name" value="{{ old('name', $recipe->name ?? '') }}" required class="mt-1 block w-full input-style @error('name') border-red-500 @enderror">
            </div>
            <div class="mt-4">
                <label for="category_id" class="block text-sm font-medium text-gray-700">Categoría</label>
                <select name="category_id" id="category_id" required class="mt-1 block w-full input-style @error('category_id') border-red-500 @enderror">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $recipe->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} @if($category->trashed()) (Oculta) @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mt-4">
                <label for="short_description" class="block text-sm font-medium text-gray-700">Descripción Corta (Preview)</label>
                <textarea name="short_description" id="short_description" rows="3" required class="mt-1 block w-full input-style @error('short_description') border-red-500 @enderror">{{ old('short_description', $recipe->short_description ?? '') }}</textarea>
            </div>
            <div class="mt-4">
                <label for="long_description" class="block text-sm font-medium text-gray-700">Descripción Larga (Página Principal)</label>
                <textarea name="long_description" id="long_description" rows="6" required class="mt-1 block w-full input-style @error('long_description') border-red-500 @enderror">{{ old('long_description', $recipe->long_description ?? '') }}</textarea>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium mb-4 border-b pb-2">Ingredientes y Pasos</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700">Ingredientes</label>
                <div id="ingredients-wrapper" class="space-y-2 mt-1">
                    @forelse (old('ingredients', $recipe->ingredients ?? ['']) as $ingredient)
                    <div class="draggable-item flex items-center space-x-2">
                        <span class="drag-handle cursor-move text-gray-400"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg></span>
                        <input type="text" name="ingredients[]" placeholder="Ej: 2 tazas de harina" value="{{ $ingredient }}" class="block w-full input-style">
                        <button type="button" class="remove-item-btn p-2 text-gray-400 hover:text-red-600 rounded-full hover:bg-red-50"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg></button>
                    </div>
                    @empty
                    @endforelse
                </div>
                <button type="button" id="add-ingredient" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium">+ Añadir ingrediente</button>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700">Pasos de Preparación</label>
                <div id="steps-wrapper" class="space-y-2 mt-1">
                    @forelse (old('steps', $recipe->steps ?? ['']) as $step)
                    <div class="draggable-item flex items-start space-x-2">
                        <span class="drag-handle cursor-move text-gray-400 pt-2"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg></span>
                        <textarea name="steps[]" rows="2" placeholder="Ej: Mezclar los ingredientes secos" class="block w-full input-style">{{ $step }}</textarea>
                        <button type="button" class="remove-item-btn p-2 text-gray-400 hover:text-red-600 rounded-full hover:bg-red-50"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg></button>
                    </div>
                    @empty
                    @endforelse
                </div>
                <button type="button" id="add-step" class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 font-medium">+ Añadir paso</button>
            </div>
        </div>
    </div>

    <div class="space-y-6">
         <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium mb-4 border-b pb-2">Detalles</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="difficulty" class="block text-sm font-medium text-gray-700">Dificultad</label>
                    <select name="difficulty" id="difficulty" required class="mt-1 block w-full input-style">
                        <option value="Fácil" {{ old('difficulty', $recipe->difficulty ?? '') == 'Fácil' ? 'selected' : '' }}>Fácil</option>
                        <option value="Media" {{ old('difficulty', $recipe->difficulty ?? '') == 'Media' ? 'selected' : '' }}>Media</option>
                        <option value="Difícil" {{ old('difficulty', $recipe->difficulty ?? '') == 'Difícil' ? 'selected' : '' }}>Difícil</option>
                    </select>
                </div>
                <div>
                    <label for="prep_time" class="block text-sm font-medium text-gray-700">Tiempo</label>
                    <input type="text" name="prep_time" id="prep_time" value="{{ old('prep_time', $recipe->prep_time ?? '') }}" required class="mt-1 block w-full input-style" placeholder="Ej: 45 minutos">
                </div>
                <div class="col-span-2">
                     <label for="servings" class="block text-sm font-medium text-gray-700">Porciones</label>
                    <input type="text" name="servings" id="servings" value="{{ old('servings', $recipe->servings ?? '') }}" required class="mt-1 block w-full input-style" placeholder="Ej: 4 personas">
                </div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium mb-4 border-b pb-2">Imagen de la Receta</h3>
            <div>
                <label for="image_url" class="block text-sm font-medium text-gray-700">URL de la Imagen</label>
                <input type="url" name="image_url" id="image_url_input" value="{{ old('image_url', $recipe->image_url ?? '') }}" required class="mt-1 block w-full input-style">
            </div>
            <div class="mt-4"><h4 class="text-sm font-medium text-gray-600">Preview</h4><img id="image_preview_small" src="{{ $recipe->image_url ?? 'https://via.placeholder.com/150' }}" alt="Preview" class="mt-2 w-full h-32 object-cover rounded-md"></div>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium mb-4 border-b pb-2">Aportes Calóricos</h3>
             <div class="grid grid-cols-2 gap-4 text-sm">
                <div><label for="calories">Calorías</label><input type="number" id="calories" name="calories" value="{{ old('calories', $recipe->calories ?? '') }}" class="mt-1 block w-full input-style"></div>
                <div><label for="proteins">Proteínas (g)</label><input type="number" id="proteins" name="proteins" value="{{ old('proteins', $recipe->proteins ?? '') }}" class="mt-1 block w-full input-style"></div>
                <div><label for="fats">Grasas (g)</label><input type="number" id="fats" name="fats" value="{{ old('fats', $recipe->fats ?? '') }}" class="mt-1 block w-full input-style"></div>
                <div><label for="carbohydrates">Carbs (g)</label><input type="number" id="carbohydrates" name="carbohydrates" value="{{ old('carbohydrates', $recipe->carbohydrates ?? '') }}" class="mt-1 block w-full input-style"></div>
                <div class="col-span-2"><label for="fiber">Fibra (g)</label><input type="number" id="fiber" name="fiber" value="{{ old('fiber', $recipe->fiber ?? '') }}" class="mt-1 block w-full input-style"></div>
            </div>
        </div>
    </div>
</div>
<div class="mt-8 flex justify-end"><a href="{{ route('recipes.index') }}" class="text-gray-600 mr-4 py-2 px-4">Cancelar</a><button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 font-medium">{{ isset($recipe) ? 'Actualizar Receta' : 'Guardar Receta' }}</button></div>

<style> .input-style { padding: 0.5rem 0.75rem; border: 1px solid #D1D5DB; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); } .input-style:focus { outline: none; border-color: #6366F1; box-shadow: 0 0 0 1px #6366F1; } .sortable-ghost { opacity: 0.4; background: #c8ebfb; } </style>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- 1. LÓGICA DE LA IA MÁGICA ---
        const btnGenerate = document.getElementById('btn_generate_ai');
        const promptInput = document.getElementById('ai_prompt');

        if(btnGenerate) {
            btnGenerate.addEventListener('click', async function(e) {
                e.preventDefault();
                const promptValue = promptInput.value;

                if(!promptValue) {
                    alert('Papi, escribe qué receta quieres generar.');
                    return;
                }

                btnGenerate.innerText = 'Cocinando... 🍳';
                btnGenerate.disabled = true;

                try {
                    const response = await fetch('/generate-recipe-ai', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ prompt: promptValue })
                    });

                    const data = await response.json();

                    if (!response.ok) throw new Error(data.error || 'Error del servidor');

                    if (data.success && data.recipe) {
                        // Llenar campos de texto
                        document.getElementById('name').value = data.recipe.name || '';
                        document.getElementById('short_description').value = data.recipe.short_description || '';
                        document.getElementById('long_description').value = data.recipe.long_description || '';
                        document.getElementById('difficulty').value = data.recipe.difficulty || 'Media';
                        document.getElementById('prep_time').value = data.recipe.prep_time || '';
                        document.getElementById('servings').value = data.recipe.servings || '';

                        // Filtro mágico: extrae SOLO los números (quita "kcal", "g", etc.)
                        const extractNumber = (val) => val ? String(val).replace(/\D/g, '') : '';

                        // Llenar Dificultad (La IA ahora mandará Fácil, Media o Difícil)
                        document.getElementById('difficulty').value = data.recipe.difficulty || 'Media';

                        // Llenar Nutrición forzando solo números
                        document.getElementById('calories').value = extractNumber(data.recipe.calories);
                        document.getElementById('proteins').value = extractNumber(data.recipe.proteins);
                        document.getElementById('fats').value = extractNumber(data.recipe.fats);
                        document.getElementById('carbohydrates').value = extractNumber(data.recipe.carbohydrates);
                        document.getElementById('fiber').value = extractNumber(data.recipe.fiber);

                        // Llenar Ingredientes (Dinámicos)
                        const ingWrapper = document.getElementById('ingredients-wrapper');
                        ingWrapper.innerHTML = '';
                        if(data.recipe.ingredients && Array.isArray(data.recipe.ingredients)) {
                            data.recipe.ingredients.forEach(ing => {
                                ingWrapper.insertAdjacentHTML('beforeend', ingredientTemplate);
                                const inputs = ingWrapper.querySelectorAll('input[name="ingredients[]"]');
                                inputs[inputs.length - 1].value = ing;
                            });
                        }

                        // Llenar Pasos (Dinámicos)
                        const stepsWrapper = document.getElementById('steps-wrapper');
                        stepsWrapper.innerHTML = '';
                        if(data.recipe.steps && Array.isArray(data.recipe.steps)) {
                            data.recipe.steps.forEach(step => {
                                stepsWrapper.insertAdjacentHTML('beforeend', stepTemplate);
                                const textareas = stepsWrapper.querySelectorAll('textarea[name="steps[]"]');
                                textareas[textareas.length - 1].value = step;
                            });
                        }

                        alert('¡Magia completada! Revisa los campos.');
                    }
                } catch (error) {
                    console.error(error);
                    alert("Uy, falló algo: " + error.message);
                } finally {
                    btnGenerate.innerText = '¡Generar Receta! ✨';
                    btnGenerate.disabled = false;
                }
            });
        }

        // --- 2. LÓGICA DE IMAGEN Y LISTAS ARRASTRABLES (Sin cambios) ---
        const imageUrlInput = document.getElementById('image_url_input');
        const imagePreviewSmall = document.getElementById('image_preview_small');
        const placeholderSmall = 'https://via.placeholder.com/150';

        imageUrlInput.addEventListener('input', function() {
            imagePreviewSmall.src = this.value || placeholderSmall;
        });

        const trashIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v6a1 1 0 11-2 0V8z" clip-rule="evenodd" /></svg>`;
        const dragIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>`;

        const ingredientTemplate = `<div class="draggable-item flex items-center space-x-2"><span class="drag-handle cursor-move text-gray-400">${dragIcon}</span><input type="text" name="ingredients[]" class="block w-full input-style"><button type="button" class="remove-item-btn p-2 text-gray-400 hover:text-red-600 rounded-full hover:bg-red-50">${trashIcon}</button></div>`;
        const stepTemplate = `<div class="draggable-item flex items-start space-x-2"><span class="drag-handle cursor-move text-gray-400 pt-2">${dragIcon}</span><textarea name="steps[]" rows="2" class="block w-full input-style"></textarea><button type="button" class="remove-item-btn p-2 text-gray-400 hover:text-red-600 rounded-full hover:bg-red-50">${trashIcon}</button></div>`;

        function setupDynamicList(wrapperId, buttonId, template) {
            const wrapper = document.getElementById(wrapperId);
            const addButton = document.getElementById(buttonId);
            wrapper.addEventListener('click', function(e) {
                if (e.target.closest('.remove-item-btn')) {
                    e.target.closest('.draggable-item').remove();
                }
            });
            addButton.addEventListener('click', function() {
                wrapper.insertAdjacentHTML('beforeend', template);
            });
            new Sortable(wrapper, {
                handle: '.drag-handle',
                animation: 150,
                ghostClass: 'sortable-ghost'
            });
        }
        setupDynamicList('ingredients-wrapper', 'add-ingredient', ingredientTemplate);
        setupDynamicList('steps-wrapper', 'add-step', stepTemplate);
    });
</script>
