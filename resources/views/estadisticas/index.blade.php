{{-- Página principal del módulo de estadísticas.
     Enlaces a: Ocupación de casilleros y Reporte por grupo. Acceso: admin, tutor. --}}
@extends('plantilla')

@section('titulo', 'Estadísticas - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Estadísticas</h2>
        <p class="muted">Genera reportes de ocupación y estadísticas del sistema.</p>

        <div class="grid grid-2" style="margin-top: 24px;">
            <a href="{{ route('estadisticas.ocupacion') }}" class="btn">Reporte de Ocupación</a>
            <a href="{{ route('estadisticas.por_grupo') }}" class="btn">Reporte por Grupo</a>
        </div>
    </div>
@endsection
