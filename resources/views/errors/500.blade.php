{{-- Página de error 500 - Error interno del servidor. --}}
@extends('plantilla')

@section('titulo', '500 - Error del servidor')

@section('contenido')
    <div class="card" style="text-align: center;">
        <h2>Error 500</h2>
        <p class="muted">Ocurrió un error interno. Intenta de nuevo en unos minutos.</p>
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
