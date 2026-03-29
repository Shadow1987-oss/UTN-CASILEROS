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
                <div class="detail-row"><strong>Nombre:</strong> {{ $student->full_name }}</div>
                <div class="detail-row"><strong>Matrícula:</strong> {{ $student->matricula_display }}</div>
                <div class="detail-row"><strong>Carrera:</strong> {{ optional($student->career)->nombre_carre ?? 'N/A' }}
                </div>
                <div class="detail-row"><strong>Cuatrimestre:</strong> {{ $student->cuatrimestre ?? '-' }}</div>
                <div class="detail-row"><strong>Grupo:</strong> {{ $student->grupo ?? '-' }}</div>
            </div>

            @if ($assignment)
                <div class="details">
                    <div class="detail-row"><strong>Casillero:</strong>
                        {{ optional($assignment->locker)->numeroCasiller ?? '-' }}</div>
                    <div class="detail-row"><strong>Ubicación:</strong>
                        {{ optional(optional($assignment->locker)->building)->num_edific ?? 'Sin edificio' }}</div>
                    <div class="detail-row"><strong>Área:</strong> {{ optional($assignment->locker)->area ?? '-' }}</div>
                    <div class="detail-row"><strong>Planta:</strong>
                        {{ optional($assignment->locker)->planta ? ucfirst(optional($assignment->locker)->planta) : '-' }}
                    </div>
                    <div class="detail-row"><strong>Estado:</strong>
                        {{ optional($assignment->locker)->estado ? ucfirst(optional($assignment->locker)->estado) : '-' }}
                    </div>
                    <div class="detail-row"><strong>Observaciones:</strong>
                        {{ optional($assignment->locker)->observaciones ?? '-' }}
                    </div>
                </div>
            @else
                <p class="muted">No tienes un casillero activo asignado actualmente.</p>
            @endif

            <div style="margin-top: 20px;">
                <h3>Solicitar casillero</h3>
                <form class="form" method="POST" action="{{ route('student.request_locker') }}">
                    @csrf
                    <div class="field">
                        <label for="idperiodo">Período</label>
                        <select id="idperiodo" name="idperiodo" class="input" required>
                            <option value="">Seleccionar período</option>
                            @foreach ($periods as $period)
                                <option value="{{ $period->idperiodo }}"
                                    {{ old('idperiodo') == $period->idperiodo ? 'selected' : '' }}>
                                    {{ $period->nombrePerio }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="observaciones">Observaciones (opcional)</label>
                        <textarea id="observaciones" name="observaciones" class="input" rows="3" maxlength="255">{{ old('observaciones') }}</textarea>
                    </div>
                    <div class="actions">
                        <button class="btn" type="submit">Enviar solicitud</button>
                    </div>
                </form>
            </div>

            <div style="margin-top: 20px;">
                <h3>Historial de solicitudes</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Período</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                            <th>Revisión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($lockerRequests ?? collect()) as $lockerRequest)
                            <tr>
                                <td>{{ optional($lockerRequest->created_at)->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>{{ optional($lockerRequest->period)->nombrePerio ?? '-' }}</td>
                                <td><span class="pill">{{ $lockerRequest->status_label }}</span></td>
                                <td>{{ $lockerRequest->observaciones ?? '-' }}</td>
                                <td>
                                    @if ($lockerRequest->reviewed_at)
                                        {{ $lockerRequest->reviewed_at->format('d/m/Y H:i') }}
                                        por {{ optional($lockerRequest->reviewer)->name ?? 'Tutor' }}
                                        @if ($lockerRequest->review_notes)
                                            <br><span class="muted">{{ $lockerRequest->review_notes }}</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="muted">Aún no tienes solicitudes registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div style="margin-top: 16px;">
                    {{ $lockerRequests->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
