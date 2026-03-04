@extends('plantilla')

@section('titulo', 'Editar Estudiante - UTN Lockers')

@section('contenido')
<div class="card">
    <h2>Editar estudiante</h2>
    <form class="form" method="POST" action="{{ route('students.update', $student) }}" id="student-form">
        @csrf
        @method('PUT')
        @include('partials.form-field', ['label' => 'Nombre', 'name' => 'nombre', 'value' => old('nombre', $student->nombre), 'placeholder' => 'Nombre completo', 'required' => true])
        @include('partials.form-field', ['label' => 'Matrícula', 'name' => 'matricula', 'value' => old('matricula', $student->matricula), 'placeholder' => 'Ej. 20190001', 'required' => true])
        @include('partials.form-field', ['label' => 'Carrera (id)', 'name' => 'idcarrera', 'value' => old('idcarrera', $student->idcarrera), 'placeholder' => 'ID Carrera' ])
        @include('partials.form-field', ['label' => 'Cuatrimestre', 'name' => 'cuatrimestre', 'value' => old('cuatrimestre', $student->cuatrimestre), 'placeholder' => 'Ej. 3'])
        @include('partials.form-field', ['label' => 'Apellido Paterno', 'name' => 'apellidoPaterno', 'value' => old('apellidoPaterno', $student->apellidoPaterno), 'placeholder' => 'Apellido paterno'])
        @include('partials.form-field', ['label' => 'Apellido Materno', 'name' => 'apellidoMaterno', 'value' => old('apellidoMaterno', $student->apellidoMaterno), 'placeholder' => 'Apellido materno'])
        @include('partials.form-field', ['label' => 'Teléfono', 'name' => 'numero_telefono', 'value' => old('numero_telefono', $student->numero_telefono), 'placeholder' => 'Ej. 3411234567'])
        <div class="actions">
            <button class="btn" type="submit">Actualizar</button>
            <a class="btn secondary" href="{{ route('students.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
