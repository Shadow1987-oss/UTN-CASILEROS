@extends('plantilla')

@section('titulo', 'Reporte de Ocupación - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Reporte de Ocupación</h2>
                <p class="muted">Estadísticas de casilleros y asignaciones.</p>
            </div>
            <a href="{{ route('reports.occupancy.export') }}" class="btn">Exportar PDF</a>
        </div>

        <div class="grid grid-2">
            <div class="stat">
                <h3>Total Casilleros</h3>
                <p>{{ $data['total_lockers'] }}</p>
            </div>
            <div class="stat">
                <h3>Disponibles</h3>
                <p>{{ $data['available'] }}</p>
            </div>
            <div class="stat">
                <h3>Ocupados</h3>
                <p>{{ $data['occupied'] }}</p>
            </div>
            <div class="stat">
                <h3>Dañados</h3>
                <p>{{ $data['damaged'] }}</p>
            </div>
            <div class="stat">
                <h3>Asignaciones Activas</h3>
                <p>{{ $data['active_assignments'] }}</p>
            </div>
        </div>
    </div>
@endsection
