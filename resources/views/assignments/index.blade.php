@extends('plantilla')
@section('titulo', 'Asignaciones - UTN Lockers')
@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Asignaciones</h2>
                <p class="muted">Asignaciones de casilleros por período.</p>
            </div>
            <a class="btn" href="{{ route('assignments.create') }}">Nueva asignación</a>
        </div>
        <form method="GET" action="{{ route('assignments.index') }}" class="form" style="margin-bottom: 16px;">
            <div class="grid grid-3">
                <div class="field">
                    <label for="idPeriodo">Período</label>
                    <select id="idPeriodo" name="idPeriodo" class="input">
                        <option value="">Todos</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->idperiodo }}"
                                {{ request('idPeriodo') == $period->idperiodo ? 'selected' : '' }}>
                                {{ $period->nombrePerio }}
                            </option>
                        @endforeach
                    </select>
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
            </div>
            <div class="actions">
                <button class="btn" type="submit">Filtrar</button>
                <a class="btn secondary" href="{{ route('assignments.index') }}">Limpiar</a>
            </div>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Matrícula</th>
                    <th>Casillero</th>
                    <th>Período</th>
                    <th>Usuario responsable</th>
                    <th>Fecha asignación</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assignments as $assignment)
                    <tr>
                        <td>{{ $assignment->student->nombre ?? '-' }} {{ $assignment->student->apellidoPaterno ?? '' }}
                        </td>
                        <td>{{ $assignment->matricula }}</td>
                        <td>#{{ $assignment->locker->numeroCasiller ?? '-' }}</td>
                        <td>{{ $assignment->period->nombrePerio ?? '-' }}</td>
                        <td>{{ optional($assignment->usuario)->nombre ?? '-' }}
                            {{ optional($assignment->usuario)->apellidoP ?? '' }}</td>
                        <td>{{ $assignment->fechaAsignacion ? $assignment->fechaAsignacion->format('d/m/Y') : '-' }}</td>
                        <td><span class="pill">{{ $assignment->status_label }}</span></td>
                        <td class="actions">
                            <a class="btn secondary" href="{{ route('assignments.edit', $assignment) }}">Editar</a>
                            @if (!$assignment->released_at)
                                <form method="POST" action="{{ route('assignments.release', $assignment) }}"
                                    style="display: inline;">
                                    @csrf
                                    <button class="btn" type="submit">Liberar</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('assignments.destroy', $assignment) }}"
                                onsubmit="return confirm('¿Eliminar esta asignación?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="muted">Sin asignaciones aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
