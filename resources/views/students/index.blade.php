@extends('plantilla')

@section('titulo', 'Estudiantes - UTN Lockers')

@section('contenido')
<div class="card">
    <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <div>
            <h2>Estudiantes</h2>
            <p class="muted">Administrar registros de estudiantes.</p>
        </div>
        <a class="btn" href="{{ route('students.create') }}">Nuevo estudiante</a>
    </div>

    <form class="form" method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data" style="margin-bottom: 20px;" id="students-import-form">
        @csrf
        <div>
            <label for="file">Importar CSV o XLSX</label>
            <input id="file" type="file" name="file" accept=".csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv" required>
            <p class="muted">Encabezados: nombre, matricula, idcarrera, cuatrimestre, numero_telefono (opcional).</p>
        </div>
        <button class="btn secondary" type="submit">Importar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Matrícula</th>
                <th>Programa</th>
                <th>Semestre</th>
                <th>Correo</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($students as $student)
            <tr>
                <td>{{ $student->name }}</td>
                <td>{{ $student->enrollment }}</td>
                <td>{{ $student->program }}</td>
                <td>{{ $student->term }}</td>
                <td>{{ $student->email ?? '-' }}</td>
                <td class="actions">
                    <a class="btn secondary" href="{{ route('students.edit', $student) }}">Editar</a>
                    <form method="POST" action="{{ route('students.destroy', $student) }}" onsubmit="return confirm('¿Eliminar este estudiante?');" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn danger" type="submit">Eliminar</button>
                    </form>
                <tr>
                    <td>{{ $student->nombre }}</td>
                    <td>{{ $student->matricula }}</td>
                    <td>{{ $student->idcarrera }}</td>
                    <td>{{ $student->cuatrimestre }}</td>
                    <td>{{ $student->numero_telefono ?? '-' }}</td>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
