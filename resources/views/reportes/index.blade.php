@extends('plantilla')

@section('titulo', 'Reportes - UTN Lockers')

@section('contenido')
<div class="card">
    <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <div>
            <h2>Reportes</h2>
            <p class="muted">Listado de reportes.</p>
        </div>
        <a class="btn" href="{{ route('reportes.create') }}">Nuevo reporte</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Descripción</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
            <tr>
                <td>{{ $report->idreporte }}</td>
                <td>{{ optional($report->usuario)->nombre ?? $report->idusuario }}</td>
                <td>{{ $report->descripcion }}</td>
                <td class="actions">
                    <a class="btn secondary" href="{{ route('reportes.edit', $report) }}">Editar</a>
                    <form method="POST" action="{{ route('reportes.destroy', $report) }}" onsubmit="return confirm('¿Eliminar este reporte?');" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn danger">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="muted">Sin reportes aún.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
