{{-- Formulario de edición de tutor. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Editar Tutor - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Editar tutor</h2>
        <form class="form" method="POST" action="{{ route('usuarios.update', $usuario) }}" id="usuario-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="idusuario" value="{{ old('idusuario', $usuario->idusuario) }}">
            @include('partials.form-field', [
                'label' => 'Nombre(s)',
                'name' => 'nombre',
                'value' => old('nombre', $usuario->nombre),
                'required' => true,
                'placeholder' => 'Ej. Juan Carlos',
                'maxlength' => 50,
            ])
            @include('partials.form-field', [
                'label' => 'Apellido Paterno',
                'name' => 'apellidoP',
                'value' => old('apellidoP', $usuario->apellidoP),
                'maxlength' => 50,
            ])
            @include('partials.form-field', [
                'label' => 'Apellido Materno',
                'name' => 'apellidoM',
                'value' => old('apellidoM', $usuario->apellidoM),
                'maxlength' => 50,
            ])
            <div class="field">
                <label for="cargo">Cargo</label>
                <select id="cargo" name="cargo" class="input">
                    <option value="">Seleccionar cargo</option>
                    <option value="Tutor" {{ old('cargo', $usuario->cargo) == 'Tutor' ? 'selected' : '' }}>Tutor</option>
                    <option value="Admin" {{ old('cargo', $usuario->cargo) == 'Admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('cargo')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            <div class="actions">
                <button class="btn" type="submit">Actualizar</button>
                <a class="btn secondary" href="{{ route('usuarios.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
