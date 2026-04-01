{{-- Página de error 500 - Error interno del servidor. --}}
@extends('plantilla')

@section('titulo', '500 - Error del servidor')

@section('contenido')
    <div class="card" style="text-align: center;">
        <h2>Error 500</h2>
        <p class="muted">Ocurrió un error interno. Intenta de nuevo en unos minutos.</p>
        <a href="{{ route('dashboard') }}" class="btn">Ir al tablero</a>
    </div>
@endsection
