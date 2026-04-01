{{-- Listado paginado de sanciones con tutor y estudiante asociados.
     Acciones: crear, editar, eliminar. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Sanciones - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Sanciones</h2>
                <p class="muted">Listado de sanciones.</p>
            </div>
            <a class="btn" href="{{ route('sanciones.create') }}">Nueva sanción</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tutor responsable</th>
                    <th>Estudiante sancionado</th>
                    <th>Sanción</th>
                    <th>Motivo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($sanciones as $sancion)
                    <tr>
                        <td>{{ $sancion->idsancion }}</td>
                        <td>{{ optional($sancion->usuario)->nombre ?? '-' }}
                            {{ optional($sancion->usuario)->apellidoP ?? '' }}
                            ({{ optional($sancion->usuario)->cargo ?? 'Sin cargo' }})
                        </td>
                        <td>
                            @if ($sancion->receipt && $sancion->receipt->student)
                                {{ $sancion->receipt->student->matricula_display }} -
                                {{ $sancion->receipt->student->full_name }}
                                ({{ $sancion->receipt->student->grupo ?? '-' }})
                            @elseif ($sancion->receipt)
                                {{ \App\Models\Student::formatMatricula($sancion->receipt->matricula) }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $sancion->sancion }}</td>
                        <td>{{ $sancion->motivo }}</td>
                        <td class="actions">
                            <a class="btn secondary" href="{{ route('sanciones.edit', $sancion) }}">Editar</a>
                            <form method="POST" action="{{ route('sanciones.destroy', $sancion) }}"
                                onsubmit="return confirm('¿Eliminar esta sanción?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">Sin sanciones aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 16px;">
            {{ $sanciones->links() }}
        </div>
    </div>
@endsection
