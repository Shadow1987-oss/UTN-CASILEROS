@extends('plantilla')

@section('titulo', '403 - Acceso denegado')

@section('contenido')
    <div class="card" style="text-align: center;">
        <h2>Error 403</h2>
        <p class="muted">No tienes permisos para acceder a esta sección.</p>
        <a href="{{ route('dashboard') }}" class="btn">Ir al tablero</a>
    </div>
@endsection
