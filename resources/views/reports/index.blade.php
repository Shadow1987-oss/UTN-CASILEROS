@extends('plantilla')

@section('titulo', 'Reportes - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Reportes Estadísticos</h2>
        <p class="muted">Genera reportes de ocupación y estadísticas del sistema.</p>

        <div class="grid grid-2" style="margin-top: 24px;">
            <a href="{{ route('reports.occupancy') }}" class="btn">Reporte de Ocupación</a>
            <a href="{{ route('reports.by_group') }}" class="btn">Reporte por Grupo</a>
        </div>
    </div>
@endsection
