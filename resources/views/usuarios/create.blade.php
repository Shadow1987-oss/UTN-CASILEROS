{{-- Formulario de creación de tutor. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Nuevo Tutor - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Nuevo tutor</h2>
        <form class="form" method="POST" action="{{ route('usuarios.store') }}">
            @csrf
            @include('partials.form-field', [
                'label' => 'ID de tutor',
                'name' => 'idusuario',
                'type' => 'number',
                'value' => old('idusuario'),
                'required' => true,
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
            @include('partials.form-field', [
                'label' => 'Cargo',
                'name' => 'cargo',
                'value' => old('cargo'),
                'maxlength' => 50,
            ])
            <div class="actions">
                <button class="btn" type="submit">Guardar</button>
                <a class="btn secondary" href="{{ route('usuarios.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
