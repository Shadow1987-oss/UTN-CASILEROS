{{-- Vista de detalle de un reporte de incidencia.
     Muestra tutor, estudiante, descripción, observaciones y casilleros afectados. --}}
@extends('plantilla')

@section('titulo', 'Detalle de Reporte - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Detalle de reporte</h2>
                <p class="muted">Información completa del reporte.</p>
            </div>
            <div>
                <a class="btn secondary" href="{{ route('reportes.edit', $report) }}">Editar</a>
                <a class="btn secondary" href="{{ route('reportes.index') }}">Volver</a>
            </div>
        </div>

        <div class="details">
            <div class="detail-row"><strong>ID:</strong> {{ $report->idreporte }}</div>
            <div class="detail-row"><strong>Tutor:</strong>
                {{ optional($report->tutor)->nombre ?? '-' }} {{ optional($report->tutor)->apellidoP ?? '' }}
            </div>
            <div class="detail-row"><strong>Cargo tutor:</strong> {{ optional($report->tutor)->cargo ?? '-' }}</div>
            <div class="detail-row"><strong>Alumno:</strong>
                @if ($report->student)
                    {{ $report->student->matricula_display }} - {{ $report->student->full_name }}
                    (Grupo: {{ $report->student->grupo ?? '-' }})
                @else
                    -
                @endif
            </div>
            <div class="detail-row"><strong>Descripción:</strong> {{ $report->descripcion }}</div>
            <div class="detail-row"><strong>Observaciones:</strong> {{ $report->observaciones ?? '-' }}</div>
            <div class="detail-row"><strong>Casilleros vinculados:</strong>
                @if ($report->casilleros->isEmpty())
                    Ninguno
                @else
                    <ul style="margin: 8px 0 0 18px;">
                        @foreach ($report->casilleros as $locker)
                            <li>
                                #{{ $locker->numeroCasiller }}
                                @if ($locker->building)
                                    (Edif. {{ $locker->building->num_edific }})
                                @endif
                                - Área: {{ $locker->area ?? '-' }}
                                - Planta: {{ $locker->planta ? ucfirst($locker->planta) : '-' }}
                                - Estado: {{ ucfirst($locker->estado) }}
                                - Observaciones: {{ $locker->observaciones ?? '-' }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
