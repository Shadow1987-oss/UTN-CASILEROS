{{-- Formulario de creación de tutor. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Nuevo Tutor - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Nuevo tutor</h2>
        <form class="form" method="POST" action="{{ route('usuarios.store') }}" id="usuario-form">
            @csrf
            @include('partials.form-field', [
                'label' => 'ID de tutor',
                'name' => 'idusuario',
                'type' => 'text',
                'value' => old('idusuario'),
                'required' => true,
                'placeholder' => 'Ej. 1, 2, 3…',
                'inputmode' => 'numeric',
            ])
            @include('partials.form-field', [
                'label' => 'Nombre(s)',
                'name' => 'nombre',
                'value' => old('nombre'),
                'required' => true,
                'placeholder' => 'Ej. Juan Carlos',
                'maxlength' => 50,
            ])
            @include('partials.form-field', [
                'label' => 'Apellido Paterno',
                'name' => 'apellidoP',
                'value' => old('apellidoP'),
                'maxlength' => 50,
            ])
            @include('partials.form-field', [
                'label' => 'Apellido Materno',
                'name' => 'apellidoM',
                'value' => old('apellidoM'),
                'maxlength' => 50,
            ])
            <div class="field">
                <label for="cargo">Cargo</label>
                <select id="cargo" name="cargo" class="input">
                    <option value="">Seleccionar cargo</option>
                    <option value="Tutor" {{ old('cargo') == 'Tutor' ? 'selected' : '' }}>Tutor</option>
                    <option value="Admin" {{ old('cargo') == 'Admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('cargo')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            <div class="actions">
                <button class="btn" type="submit">Guardar</button>
                <a class="btn secondary" href="{{ route('usuarios.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
