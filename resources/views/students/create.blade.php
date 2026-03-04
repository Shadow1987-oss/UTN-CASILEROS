@extends('plantilla')

@section('titulo', 'Nuevo Estudiante - UTN Lockers')

@section('contenido')
<div class="card">
    <h2>Nuevo estudiante</h2>
    <form class="form" method="POST" action="{{ route('students.store') }}" id="student-form">
        @csrf
        @include('partials.form-field', ['label' => 'Nombre', 'name' => 'nombre', 'value' => old('nombre'), 'placeholder' => 'Nombre completo', 'required' => true])
        @include('partials.form-field', ['label' => 'Matrícula', 'name' => 'matricula', 'value' => old('matricula'), 'placeholder' => 'Ej. 20190001', 'required' => true])
        @include('partials.form-field', ['label' => 'Carrera (id)', 'name' => 'idcarrera', 'value' => old('idcarrera'), 'placeholder' => 'ID Carrera' ])
        @include('partials.form-field', ['label' => 'Cuatrimestre', 'name' => 'cuatrimestre', 'value' => old('cuatrimestre'), 'placeholder' => 'Ej. 3'])
        @include('partials.form-field', ['label' => 'Apellido Paterno', 'name' => 'apellidoPaterno', 'value' => old('apellidoPaterno'), 'placeholder' => 'Apellido paterno'])
        @include('partials.form-field', ['label' => 'Apellido Materno', 'name' => 'apellidoMaterno', 'value' => old('apellidoMaterno'), 'placeholder' => 'Apellido materno'])
        @include('partials.form-field', ['label' => 'Teléfono', 'name' => 'numero_telefono', 'value' => old('numero_telefono'), 'placeholder' => 'Ej. 3411234567'])
        <div class="actions">
            <button class="btn" type="submit">Guardar</button>
            <a class="btn secondary" href="{{ route('students.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
