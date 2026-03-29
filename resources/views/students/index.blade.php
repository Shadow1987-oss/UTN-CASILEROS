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

        <form method="GET" action="{{ route('students.index') }}" class="form" style="margin-bottom: 20px;">
            <div class="grid grid-3">
                <div class="field">
                    <label for="search">Matrícula</label>
                    <input type="text" name="search" id="search" class="input" value="{{ request('search') }}"
                        placeholder="Ej. TIC-320072">
                </div>
                <div class="field">
                    <label for="idcarrera">Carrera</label>
                    <select id="idcarrera" name="idcarrera" class="input">
                        <option value="">Todas</option>
                        @foreach ($careers as $career)
                            <option value="{{ $career->idcarrera }}"
                                {{ request('idcarrera') == $career->idcarrera ? 'selected' : '' }}>
                                {{ $career->nombre_carre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="cuatrimestre">Cuatrimestre</label>
                    <input type="number" name="cuatrimestre" id="cuatrimestre" class="input"
                        value="{{ request('cuatrimestre') }}" placeholder="Ej. 3">
                </div>
            </div>
            <div class="grid grid-3">
                <div class="field">
                    <label for="grupo">Grupo</label>
                    <select id="grupo" name="grupo" class="input">
                        <option value="">Todos</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group }}" {{ request('grupo') == $group ? 'selected' : '' }}>
                                {{ $group }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="idedificio">Edificio</label>
                    <select id="idedificio" name="idedificio" class="input">
                        <option value="">Todos</option>
                        @foreach ($buildings as $building)
                            <option value="{{ $building->idedificio }}"
                                {{ request('idedificio') == $building->idedificio ? 'selected' : '' }}>
                                {{ $building->num_edific }}
                            </option>
                        @endforeach
                    </select>
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
            </div>
            @if (!empty($searchError))
                <div class="field-help error">{{ $searchError }}</div>
            @endif
            <div class="actions">
                <button type="submit" class="btn">Filtrar</button>
                <a class="btn secondary" href="{{ route('students.index') }}">Limpiar</a>
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
                    <p class="muted">Encabezados: nombre, matricula. Opcionales: idcarrera, cuatrimestre, grupo,
                        numero_telefonico (o numero_telefono).</p>
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
                    <th>Grupo</th>
                    <th>Teléfono</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                    <tr>
                        <td>{{ $student->full_name }}</td>
                        <td>{{ $student->matricula_display }}</td>
                        <td>{{ optional($student->career)->nombre_carre ?? $student->idcarrera }}</td>
                        <td>{{ $student->cuatrimestre }}</td>
                        <td>{{ $student->grupo ?? '-' }}</td>
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
                        <td colspan="7" class="muted">No hay estudiantes registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 16px;">
            {{ $students->links() }}
        </div>
    </div>
@endsection
