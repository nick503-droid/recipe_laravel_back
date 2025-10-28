@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Editar Receta: {{ $recipe->name }}</h1>
    <form action="{{ route('recipes.update', $recipe->id) }}" method="POST">
        @method('PUT')
        @include('recipes._form')
    </form>
@endsection