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
                {{ optional($report->tutor)->name ?? '-' }}
            </div>
            <div class="detail-row"><strong>Correo tutor:</strong> {{ optional($report->tutor)->email ?? '-' }}</div>
            <div class="detail-row"><strong>Alumno:</strong>
                @if ($report->student)
                    {{ $report->student->matricula }} - {{ $report->student->nombre }}
                    {{ $report->student->apellidoPaterno }} {{ $report->student->apellidoMaterno }}
                @else
                    -
                @endif
            </div>
            <div class="detail-row"><strong>Descripción:</strong> {{ $report->descripcion }}</div>
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
                                - {{ ucfirst($locker->estado) }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
@endsection
