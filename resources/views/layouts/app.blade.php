<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración de Recetas</title>
    <link rel="icon" type="image/png" href="{{ asset('images/mi-logo.png') }}">


    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">

    <nav class="bg-white shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('recipes.index') }}" class="font-bold text-xl text-indigo-600">🍳 RecipeAdmin</a>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('categories.index') }}" class="text-gray-500 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Categorías</a>
                    <a href="{{ route('recipes.index') }}" class="text-gray-500 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Recetas</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

</body>
</html>