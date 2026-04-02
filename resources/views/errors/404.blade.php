{{-- Página de error 404 - Recurso no encontrado. --}}
@extends('plantilla')

@section('titulo', '404 - Página no encontrada')

@section('contenido')
    <div class="card" style="text-align: center;">
        <h2>Error 404</h2>
        <p class="muted">La página que buscas no existe.</p>
        @auth
            @if (auth()->user()->role === 'estudiante')
                <a href="{{ route('student.home') }}" class="btn">Ir a mi casillero</a>
            @else
                <a href="{{ route('dashboard') }}" class="btn">Ir al tablero</a>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn">Iniciar sesión</a>
        @endauth
    </div>
@endsection
