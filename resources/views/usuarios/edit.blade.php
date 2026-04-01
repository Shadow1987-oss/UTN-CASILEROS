{{-- Formulario de edición de tutor. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Editar Tutor - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Editar tutor</h2>
        <form class="form" method="POST" action="{{ route('usuarios.update', $usuario) }}">
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
            @include('partials.form-field', [
                'label' => 'Cargo',
                'name' => 'cargo',
                'value' => old('cargo', $usuario->cargo),
                'maxlength' => 50,
            ])
            <div class="actions">
                <button class="btn" type="submit">Actualizar</button>
                <a class="btn secondary" href="{{ route('usuarios.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
