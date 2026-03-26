@extends('plantilla')

@section('titulo', 'Reporte por Grupo - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Reporte por Grupo</h2>
                <p class="muted">Estadísticas por carrera.</p>
            </div>
            <div>
                <form method="GET" action="{{ route('reports.by_group') }}" style="display: inline;">
                    <select name="idcarrera" onchange="this.form.submit()">
                        <option value="">Todas las carreras</option>
                        @foreach ($careers as $career)
                            <option value="{{ $career->idcarrera }}"
                                {{ request('idcarrera') == $career->idcarrera ? 'selected' : '' }}>
                                {{ $career->nombre_carre }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('reports.by_group.export', request()->query()) }}" class="btn">Exportar PDF</a>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Carrera</th>
                    <th>Total Estudiantes</th>
                    <th>Con Casillero</th>
                    <th>Sin Casillero</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $row)
                    <tr>
                        <td>{{ $row['career'] }}</td>
                        <td>{{ $row['total_students'] }}</td>
                        <td>{{ $row['with_lockers'] }}</td>
                        <td>{{ $row['without_lockers'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">No hay datos para los filtros seleccionados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
