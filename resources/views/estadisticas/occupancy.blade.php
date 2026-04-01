{{-- Estadística de ocupación de casilleros con filtros por edificio, área,
     planta y período. Muestra totales y desglose por edificio. Exporta a PDF. --}}
@extends('plantilla')

@section('titulo', 'Reporte de Ocupación - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Reporte de Ocupación</h2>
                <p class="muted">Estadísticas de casilleros y asignaciones.</p>
            </div>
            <a href="{{ route('estadisticas.ocupacion.exportar', request()->query()) }}" class="btn">Exportar PDF</a>
        </div>

        <form method="GET" action="{{ route('estadisticas.ocupacion') }}" class="form" style="margin-bottom: 16px;">
            <div class="grid grid-4">
                <div class="field">
                    <label for="idedificio">Edificio</label>
                    <select id="idedificio" name="idedificio" class="input">
                        <option value="">Todos</option>
                        @foreach ($buildings as $building)
                            <option value="{{ $building->idedificio }}"
                                {{ request('idedificio') == $building->idedificio ? 'selected' : '' }}>
                                {{ $building->num_edific }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="area">Área</label>
                    <select id="area" name="area" class="input">
                        <option value="">Todas</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>
                                {{ $area }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="planta">Planta</label>
                    <select id="planta" name="planta" class="input">
                        <option value="">Todas</option>
                        <option value="baja" {{ request('planta') == 'baja' ? 'selected' : '' }}>Planta baja</option>
                        <option value="alta" {{ request('planta') == 'alta' ? 'selected' : '' }}>Planta alta</option>
                    </select>
                </div>
                <div class="field">
                    <label for="idperiodo">Período</label>
                    <select id="idperiodo" name="idperiodo" class="input">
                        <option value="">Todos</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->idperiodo }}"
                                {{ request('idperiodo') == $period->idperiodo ? 'selected' : '' }}>
                                {{ $period->nombrePerio }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="actions">
                <button class="btn" type="submit">Filtrar</button>
                <a class="btn secondary" href="{{ route('estadisticas.ocupacion') }}">Limpiar</a>
            </div>
        </form>

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
            <div class="stat">
                <h3>Promedio días de ocupación</h3>
                <p>{{ $data['average_occupancy_days'] }}</p>
            </div>
        </div>

        <h3 style="margin-top: 16px;">Resumen por edificio</h3>
        <table>
            <thead>
                <tr>
                    <th>Edificio</th>
                    <th>Total</th>
                    <th>Ocupados</th>
                    <th>Dañados</th>
                    <th>Disponibles</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data['by_building'] as $row)
                    <tr>
                        <td>{{ $row['building'] }}</td>
                        <td>{{ $row['total'] }}</td>
                        <td>{{ $row['occupied'] }}</td>
                        <td>{{ $row['damaged'] }}</td>
                        <td>{{ $row['available'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No hay datos para los filtros seleccionados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
