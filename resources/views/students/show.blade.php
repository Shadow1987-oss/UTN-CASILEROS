@extends('plantilla')

@section('titulo', 'Detalles de Estudiante - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Detalles de Estudiante</h2>
                <p class="muted">Información completa del estudiante.</p>
            </div>
            <div>
                <a class="btn secondary" href="{{ route('students.edit', $student) }}">Editar</a>
                <a class="btn secondary" href="{{ route('students.index') }}">Volver</a>
            </div>
        </div>

        <div class="details">
            <div class="detail-row">
                <strong>Matrícula:</strong> {{ $student->matricula }}
            </div>
            <div class="detail-row">
                <strong>Nombre:</strong> {{ $student->nombre }}
            </div>
            <div class="detail-row">
                <strong>Carrera:</strong> {{ $student->career->nombre_carre ?? 'N/A' }}
            </div>
            <div class="detail-row">
                <strong>Cuatrimestre:</strong> {{ $student->cuatrimestre }}
            </div>
            <div class="detail-row">
                <strong>Apellido Paterno:</strong> {{ $student->apellidoPaterno }}
            </div>
            <div class="detail-row">
                <strong>Apellido Materno:</strong> {{ $student->apellidoMaterno }}
            </div>
            <div class="detail-row">
                <strong>Teléfono:</strong>
                {{ $student->numero_telefonico ?? ($student->numero_telefono ?? 'Sin teléfono registrado') }}
            </div>
        </div>

        <h3>Historial de Asignaciones</h3>
        @if ($assignments->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th>Casillero</th>
                        <th>Período</th>
                        <th>Fecha Asignación</th>
                        <th>Fecha Liberación</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assignments as $assignment)
                        <tr>
                            <td>{{ $assignment->locker->numeroCasiller ?? '-' }}</td>
                            <td>{{ $assignment->period->nombrePerio ?? '-' }}</td>
                            <td>{{ $assignment->fechaAsignacion ? $assignment->fechaAsignacion->format('d/m/Y') : '-' }}
                            </td>
                            <td>{{ $assignment->released_at ? $assignment->released_at->format('d/m/Y') : '-' }}</td>
                            <td>{{ $assignment->status_label }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No hay asignaciones actuales.</p>
        @endif
    </div>
@endsection
