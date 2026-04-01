{{-- Listado de recibos de sanciones con sanción y estudiante asociados.
     Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Recibos - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Recibos</h2>
                <p class="muted">Listado de recibos (recibe).</p>
            </div>
            <a class="btn" href="{{ route('recibe.create') }}">Nuevo recibo</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sanción</th>
                    <th>Motivo</th>
                    <th>Estudiante</th>
                    <th>Matrícula</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recibos as $r)
                    <tr>
                        <td>{{ $r->idrecibe }}</td>
                        <td>{{ optional($r->sanction)->sancion ?? $r->idsancion }}</td>
                        <td>{{ optional($r->sanction)->motivo ?? '-' }}</td>
                        <td>{{ optional($r->student)->full_name ?? '-' }}</td>
                        <td>
                            {{ optional($r->student)->matricula_display ?? \App\Models\Student::formatMatricula($r->matricula) }}
                            ({{ optional($r->student)->grupo ?? '-' }})
                        </td>
                        <td class="actions">
                            <a class="btn secondary" href="{{ route('recibe.edit', $r) }}">Editar</a>
                            <form method="POST" action="{{ route('recibe.destroy', $r) }}"
                                onsubmit="return confirm('¿Eliminar este recibo?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">Sin recibos aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
