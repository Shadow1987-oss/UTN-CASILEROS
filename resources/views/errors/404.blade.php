@extends('plantilla')

@section('titulo', '404 - Página no encontrada')

@section('contenido')
    <div class="card" style="text-align: center;">
        <h2>Error 404</h2>
        <p class="muted">La página que buscas no existe.</p>
        <a href="{{ route('dashboard') }}" class="btn">Ir al tablero</a>
    </div>
@endsection
