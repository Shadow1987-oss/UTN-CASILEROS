{{-- Formulario de creación de estudiante.
     Campos: matrícula, nombre, carrera, cuatrimestre, grupo,
     apellidos, teléfono, cuenta de usuario vinculada. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Nuevo Estudiante - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Nuevo estudiante</h2>
        <form class="form" method="POST" action="{{ route('students.store') }}" id="student-form">
            @csrf
            @include('partials.form-field', [
                'label' => 'Nombre(s)',
                'name' => 'nombre',
                'value' => old('nombre'),
                'placeholder' => 'Ej. Juan Carlos',
                'required' => true,
                'maxlength' => 50,
            ])
            @include('partials.form-field', [
                'label' => 'Matrícula',
                'name' => 'matricula',
                'value' => old('matricula'),
                'placeholder' => 'Ej. 320072',
                'required' => true,
                'maxlength' => 20,
            ])
            <div class="field">
                <label for="user_id">Cuenta de acceso (opcional)</label>
                <select id="user_id" name="user_id" class="input">
                    <option value="">Sin vincular</option>
                    @foreach ($studentUsers as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="idcarrera">Carrera</label>
                <select id="idcarrera" name="idcarrera" class="input">
                    <option value="">Seleccionar carrera</option>
                    @foreach ($careers as $career)
                        <option value="{{ $career->idcarrera }}"
                            {{ old('idcarrera') == $career->idcarrera ? 'selected' : '' }}>{{ $career->idcarrera }} -
                            {{ $career->nombre_carre }}</option>
                    @endforeach
                </select>
                @error('idcarrera')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            @include('partials.form-field', [
                'label' => 'Cuatrimestre',
                'name' => 'cuatrimestre',
                'value' => old('cuatrimestre'),
                'placeholder' => 'Ej. 3',
                'maxlength' => 2,
            ])
            @include('partials.form-field', [
                'label' => 'Grupo',
                'name' => 'grupo',
                'value' => old('grupo'),
                'placeholder' => 'Ej. DMS-51',
                'maxlength' => 10,
            ])
            @include('partials.form-field', [
                'label' => 'Apellido Paterno',
                'name' => 'apellidoPaterno',
                'value' => old('apellidoPaterno'),
                'placeholder' => 'Apellido paterno',
                'maxlength' => 50,
            ])
            @include('partials.form-field', [
                'label' => 'Apellido Materno',
                'name' => 'apellidoMaterno',
                'value' => old('apellidoMaterno'),
                'placeholder' => 'Apellido materno',
                'maxlength' => 50,
            ])
            @include('partials.form-field', [
                'label' => 'Número de Teléfono',
                'name' => 'numero_telefonico',
                'value' => old('numero_telefonico'),
                'placeholder' => 'Ej. 3411234567',
                'maxlength' => 15,
            ])
            <div class="actions">
                <button class="btn" type="submit">Guardar</button>
                <a class="btn secondary" href="{{ route('students.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
