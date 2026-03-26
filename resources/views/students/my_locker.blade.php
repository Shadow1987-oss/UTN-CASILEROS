@extends('plantilla')

@section('titulo', 'Mi Casillero - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Mi información de casillero</h2>
        <p class="muted">Consulta de estudiante.</p>

        @if (!$student)
            <div class="alert error">Tu cuenta no está vinculada a un registro de estudiante. Contacta a administración.
            </div>
        @else
            <div class="details" style="margin-bottom: 16px;">
                <div class="detail-row"><strong>Nombre:</strong> {{ $student->nombre }} {{ $student->apellidoPaterno ?? '' }}
                    {{ $student->apellidoMaterno ?? '' }}</div>
                <div class="detail-row"><strong>Matrícula:</strong> {{ $student->matricula }}</div>
            </div>

            @if ($assignment)
                <div class="details">
                    <div class="detail-row"><strong>Casillero:</strong>
                        {{ optional($assignment->locker)->numeroCasiller ?? '-' }}</div>
                    <div class="detail-row"><strong>Ubicación:</strong>
                        {{ optional(optional($assignment->locker)->building)->num_edific ?? 'Sin edificio' }}</div>
                    <div class="detail-row"><strong>Estado:</strong> {{ optional($assignment->locker)->estado ?? '-' }}
                    </div>
                </div>
            @else
                <p class="muted">No tienes un casillero activo asignado actualmente.</p>
            @endif
        @endif
    </div>
@endsection
