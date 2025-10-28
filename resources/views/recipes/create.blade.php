@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Crear Nueva Receta</h1>
    <form action="{{ route('recipes.store') }}" method="POST">
        @include('recipes._form')
    </form>
@endsection