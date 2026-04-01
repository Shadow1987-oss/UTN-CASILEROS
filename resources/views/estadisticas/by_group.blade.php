{{-- Estadística por grupo/carrera. Muestra estudiantes con y sin casillero
     agrupados por carrera. Filtros: carrera, cuatrimestre, grupo, edificio. --}}
@extends('plantilla')

@section('titulo', 'Reporte por Grupo - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Reporte por Grupo</h2>
                <p class="muted">Estadísticas por carrera.</p>
            </div>
            <div>
                <a href="{{ route('estadisticas.por_grupo.exportar', request()->query()) }}" class="btn">Exportar PDF</a>
            </div>
        </div>

        <form method="GET" action="{{ route('estadisticas.por_grupo') }}" class="form" style="margin-bottom: 16px;">
            <div class="grid grid-4">
                <div class="field">
                    <label for="idcarrera">Carrera</label>
                    <select id="idcarrera" name="idcarrera" class="input">
                        <option value="">Todas</option>
                        @foreach ($careers as $career)
                            <option value="{{ $career->idcarrera }}"
                                {{ request('idcarrera') == $career->idcarrera ? 'selected' : '' }}>
                                {{ $career->nombre_carre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="cuatrimestre">Cuatrimestre</label>
                    <input id="cuatrimestre" name="cuatrimestre" type="number" class="input" min="1" max="10"
                        value="{{ request('cuatrimestre') }}" placeholder="Ej. 3">
                </div>

                <div class="field">
                    <label for="grupo">Grupo</label>
                    <select id="grupo" name="grupo" class="input">
                        <option value="">Todos</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group }}" {{ request('grupo') == $group ? 'selected' : '' }}>
                                {{ $group }}
                            </option>
                        @endforeach
                    </select>
                </div>

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
            </div>
            <div class="actions">
                <button class="btn" type="submit">Filtrar</button>
                <a href="{{ route('estadisticas.por_grupo') }}" class="btn secondary">Limpiar</a>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Carrera</th>
                    <th>Total Estudiantes</th>
                    <th>Con Casillero</th>
                    <th>Sin Casillero</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $row)
                    <tr>
                        <td>{{ $row['career'] }}</td>
                        <td>{{ $row['total_students'] }}</td>
                        <td>{{ $row['with_lockers'] }}</td>
                        <td>{{ $row['without_lockers'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">No hay datos para los filtros seleccionados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
