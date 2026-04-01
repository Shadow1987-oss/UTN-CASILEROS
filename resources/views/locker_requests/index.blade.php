{{-- Listado de solicitudes de casillero de estudiantes.
     Filtros: estado (pendiente/aprobada/rechazada), período, matrícula.
     Acciones: aprobar o rechazar solicitudes pendientes. Acceso: admin, tutor. --}}
@extends('plantilla')

@section('titulo', 'Solicitudes de Casillero - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Solicitudes de casillero</h2>
                <p class="muted">Bandeja de revisión para administradores y tutores.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('locker_requests.index') }}" class="form" style="margin-bottom: 16px;">
            <div class="grid grid-3">
                <div class="field">
                    <label for="matricula">Matrícula</label>
                    <input id="matricula" name="matricula" type="text" class="input" value="{{ request('matricula') }}"
                        placeholder="Ej. TIC-320072">
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

                <div class="field">
                    <label for="estado">Estado</label>
                    <select id="estado" name="estado" class="input">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente
                        </option>
                        <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                        <option value="rechazada" {{ request('estado') == 'rechazada' ? 'selected' : '' }}>Rechazada
                        </option>
                    </select>
                </div>
            </div>
            <div class="actions">
                <button class="btn" type="submit">Filtrar</button>
                <a class="btn secondary" href="{{ route('locker_requests.index') }}">Limpiar</a>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Alumno</th>
                    <th>Período</th>
                    <th>Estado</th>
                    <th>Observaciones alumno</th>
                    <th>Revisión</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($requests as $requestItem)
                    <tr>
                        <td>{{ optional($requestItem->created_at)->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>
                            {{ optional($requestItem->student)->matricula_display ?? \App\Models\Student::formatMatricula($requestItem->matricula) }}
                            - {{ optional($requestItem->student)->full_name ?? '-' }}
                            (Grupo: {{ optional($requestItem->student)->grupo ?? '-' }})
                        </td>
                        <td>{{ optional($requestItem->period)->nombrePerio ?? '-' }}</td>
                        <td><span class="pill">{{ $requestItem->status_label }}</span></td>
                        <td>{{ $requestItem->observaciones ?? '-' }}</td>
                        <td>
                            @if ($requestItem->reviewed_at)
                                {{ optional($requestItem->reviewed_at)->format('d/m/Y H:i') }}
                                por {{ optional($requestItem->reviewer)->name ?? 'Usuario' }}
                                @if ($requestItem->review_notes)
                                    <br><span class="muted">{{ $requestItem->review_notes }}</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="actions">
                            @if ($requestItem->estado === 'pendiente')
                                <form method="POST" action="{{ route('locker_requests.approve', $requestItem) }}"
                                    style="display:inline; margin-right: 6px;">
                                    @csrf
                                    <input type="text" name="review_notes" class="input" maxlength="255"
                                        placeholder="Nota (opcional)" style="max-width: 180px; display:inline-block;">
                                    <button class="btn" type="submit">Aprobar</button>
                                </form>

                                <form method="POST" action="{{ route('locker_requests.reject', $requestItem) }}"
                                    style="display:inline;">
                                    @csrf
                                    <input type="text" name="review_notes" class="input" maxlength="255" required
                                        placeholder="Motivo de rechazo" style="max-width: 180px; display:inline-block;">
                                    <button class="btn danger" type="submit">Rechazar</button>
                                </form>
                            @else
                                <span class="muted">Atendida</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="muted">Sin solicitudes registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 16px;">
            {{ $requests->links() }}
        </div>
    </div>
@endsection
