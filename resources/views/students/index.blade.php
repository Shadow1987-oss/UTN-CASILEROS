@extends('plantilla')

@section('titulo', 'Estudiantes - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Estudiantes</h2>
                <p class="muted">Administrar registros de estudiantes.</p>
            </div>
        </div>

        <form method="GET" action="{{ route('students.index') }}" style="margin-bottom: 20px;">
            <div class="field">
                <label for="search">Buscar por matrícula</label>
                <input type="number" name="search" id="search" value="{{ request('search') }}" placeholder="Ej. 320072">
                <button type="submit" class="btn">Buscar</button>
                @if (!empty($searchError))
                    <div class="field-help error">{{ $searchError }}</div>
                @endif
            </div>
        </form>

        @if (auth()->user()->role === 'admin')
            <form class="form" method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data"
                style="margin-bottom: 20px;" id="students-import-form">
                @csrf
                <div>
                    <label for="file">Importar CSV o XLSX</label>
                    <input id="file" type="file" name="file"
                        accept=".csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv" required>
                    <p class="muted">Encabezados: nombre, matricula, idcarrera, cuatrimestre, numero_telefonico (o
                        numero_telefono) opcional.</p>
                </div>
                <button class="btn secondary" type="submit">Importar</button>
            </form>
        @endif

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Matrícula</th>
                    <th>Carrera</th>
                    <th>Cuatrimestre</th>
                    <th>Teléfono</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                    <tr>
                        <td>{{ $student->nombre }} {{ $student->apellidoPaterno ?? '' }}
                            {{ $student->apellidoMaterno ?? '' }}</td>
                        <td>{{ $student->matricula }}</td>
                        <td>{{ optional($student->career)->nombre_carre ?? $student->idcarrera }}</td>
                        <td>{{ $student->cuatrimestre }}</td>
                        <td>{{ $student->numero_telefonico ?? ($student->numero_telefono ?? '-') }}</td>
                        <td class="actions">
                            <a class="btn secondary" href="{{ route('students.show', $student) }}">Ver</a>
                            @if (auth()->user()->role === 'admin')
                                <a class="btn secondary" href="{{ route('students.edit', $student) }}">Editar</a>
                                <form method="POST" action="{{ route('students.destroy', $student) }}"
                                    onsubmit="return confirm('¿Eliminar este estudiante?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn danger" type="submit">Eliminar</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted">No hay estudiantes registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
