{{-- Página de error 403 - Acceso denegado (rol insuficiente). --}}
@extends('plantilla')

@section('titulo', '403 - Acceso denegado')

@section('contenido')
    <div class="card" style="text-align: center;">
        <h2>Error 403</h2>
        <p class="muted">No tienes permisos para acceder a esta sección.</p>
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
