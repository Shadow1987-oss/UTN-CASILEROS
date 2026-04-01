{{-- Listado paginado de reportes de incidencias.
     Filtros: tutor, matrícula, casillero, búsqueda libre.
     Acciones: crear, editar, ver detalle, eliminar. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Reportes - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Reportes</h2>
                <p class="muted">Listado de reportes.</p>
            </div>
            <a class="btn" href="{{ route('reportes.create') }}">Nuevo reporte</a>
        </div>

        <form method="GET" action="{{ route('reportes.index') }}" class="form" style="margin-bottom: 16px;">
            <div class="grid grid-3">
                <div class="field">
                    <label for="tutor_id">Filtrar por tutor</label>
                    <select id="tutor_id" name="tutor_id" class="input">
                        <option value="">Todos</option>
                        @foreach ($tutors as $tutor)
                            <option value="{{ $tutor->idusuario }}"
                                {{ request('tutor_id') == $tutor->idusuario ? 'selected' : '' }}>
                                {{ $tutor->nombre }} {{ $tutor->apellidoP ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="matricula">Filtrar por alumno</label>
                    <select id="matricula" name="matricula" class="input">
                        <option value="">Todos</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->matricula }}"
                                {{ request('matricula') == $student->matricula ? 'selected' : '' }}>
                                {{ $student->matricula_display }} - {{ $student->full_name }}
                                ({{ $student->grupo ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="idcasillero">Filtrar por casillero</label>
                    <select id="idcasillero" name="idcasillero" class="input">
                        <option value="">Todos</option>
                        @foreach ($lockers as $locker)
                            <option value="{{ $locker->idcasillero }}"
                                {{ request('idcasillero') == $locker->idcasillero ? 'selected' : '' }}>
                                #{{ $locker->numeroCasiller }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="field">
                <label for="search">Buscar por ID, descripción u observaciones</label>
                <input id="search" name="search" type="text" class="input" value="{{ request('search') }}">
            </div>

            <div class="actions">
                <button class="btn" type="submit">Filtrar</button>
                <a class="btn secondary" href="{{ route('reportes.index') }}">Limpiar</a>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tutor</th>
                    <th>Alumno</th>
                    <th>Descripción</th>
                    <th>Observaciones</th>
                    <th>Casilleros vinculados</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>{{ $report->idreporte }}</td>
                        <td>{{ optional($report->tutor)->nombre ?? '-' }} {{ optional($report->tutor)->apellidoP ?? '' }}
                        </td>
                        <td>
                            @if ($report->student)
                                {{ $report->student->matricula_display }} - {{ $report->student->full_name }}
                                ({{ $report->student->grupo ?? '-' }})
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $report->descripcion }}</td>
                        <td>{{ $report->observaciones ?? '-' }}</td>
                        <td>{{ $report->casilleros->count() }}</td>
                        <td class="actions">
                            <a class="btn secondary" href="{{ route('reportes.show', $report) }}">Ver</a>
                            <a class="btn secondary" href="{{ route('reportes.edit', $report) }}">Editar</a>
                            <form method="POST" action="{{ route('reportes.destroy', $report) }}"
                                onsubmit="return confirm('¿Eliminar este reporte?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="muted">Sin reportes aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 16px;">
            {{ $reports->links() }}
        </div>
    </div>
@endsection
