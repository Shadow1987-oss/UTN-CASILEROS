{{-- Formulario de registro público.
     Solo para alumnos con correo institucional UTNay.
     Extrae la matrícula automáticamente del correo. --}}
@extends('plantilla')

@section('titulo', 'Registro - UTN Lockers')

@section('menu')

@endsection

@section('contenido')
    <div class="card" style="max-width: 560px; margin: 0 auto;">
        <h2>Crear cuenta</h2>
        <p class="muted">Registro para estudiantes y tutores. El rol se asigna automáticamente por correo institucional.</p>

        <form class="form" method="POST" action="{{ route('register.attempt') }}">
            @csrf

            @include('partials.form-field', [
                'label' => 'Correo',
                'name' => 'email',
                'type' => 'email',
                'value' => old('email'),
                'placeholder' => 'Alumno: tic-320072@utnay.edu.mx | Tutor: nombre.apellido@utnay.edu.mx',
                'required' => true,
            ])

            <div class="field" id="nombre-field">
                <label for="nombre">Nombre(s)</label>
                <input id="nombre" type="text" name="nombre" value="{{ old('nombre') }}" class="input"
                    placeholder="Ej. Juan Carlos" required maxlength="50">
                @error('nombre')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field" id="apellidoPaterno-field">
                <label for="apellidoPaterno">Apellido paterno</label>
                <input id="apellidoPaterno" type="text" name="apellidoPaterno" value="{{ old('apellidoPaterno') }}"
                    class="input" placeholder="Ej. Pérez" required maxlength="50">
                @error('apellidoPaterno')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field" id="apellidoMaterno-field">
                <label for="apellidoMaterno">Apellido materno (opcional)</label>
                <input id="apellidoMaterno" type="text" name="apellidoMaterno" value="{{ old('apellidoMaterno') }}"
                    class="input" placeholder="Ej. López" maxlength="50">
                @error('apellidoMaterno')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>

            @include('partials.form-field', [
                'label' => 'Contraseña',
                'name' => 'password',
                'type' => 'password',
                'required' => true,
            ])

            @include('partials.form-field', [
                'label' => 'Confirmar contraseña',
                'name' => 'password_confirmation',
                'type' => 'password',
                'required' => true,
            ])

            <div class="actions">
                <button class="btn" type="submit">Registrarme</button>
                <a class="btn secondary" href="{{ route('login') }}">Ya tengo cuenta</a>
            </div>
        </form>
    </div>
@endsection
