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

    <table>
        <thead>
            <tr>
                <th>Estudiante</th>
                <th>Matrícula</th>
                <th>Casillero</th>
                <th>Período</th>
                <th>Estado</th>
                <th>Asignado</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assignments as $assignment)
            <tr>
                <td>{{ $assignment->student->nombre ?? '-' }}</td>
                <td>{{ $assignment->matricula ?? ($assignment->student->matricula ?? '-') }}</td>
                <td>{{ $assignment->locker->numeroCasiller ?? '-' }}</td>
                <td>{{ $assignment->period->nombrePerio ?? '-' }}</td>
                <td><span class="pill">{{ $assignment->status ?? '-' }}</span></td>
                <td>{{ $assignment->fechaAsignacion ?? '-' }}</td>
                <td class="actions">
                    <a class="btn secondary" href="{{ route('assignments.edit', $assignment) }}">Editar</a>
                    @if (!$assignment->released_at)
                    <form method="POST" action="{{ route('assignments.release', $assignment) }}" style="display: inline;">
                        @csrf
                        <button class="btn" type="submit">Liberar</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('assignments.destroy', $assignment) }}" onsubmit="return confirm('¿Eliminar esta asignación?');" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn danger" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="muted">Sin asignaciones aún.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
