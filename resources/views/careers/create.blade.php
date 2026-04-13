{{-- Formulario de creación de carrera. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Nueva Carrera - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Nueva carrera</h2>
        <form class="form" method="POST" action="{{ route('careers.store') }}" id="career-form">
            @csrf
            @include('partials.form-field', [
                'label' => 'ID de carrera',
                'name' => 'idcarrera',
                'type' => 'text',
                'value' => old('idcarrera'),
                'required' => true,
                'placeholder' => 'Ej. 1, 2, 3…',
                'inputmode' => 'numeric',
            ])
            @include('partials.form-field', [
                'label' => 'Nombre de carrera',
                'name' => 'nombre_carre',
                'value' => old('nombre_carre'),
                'required' => true,
                'maxlength' => 50,
            ])
            <div class="actions">
                <button class="btn" type="submit">Guardar</button>
                <a class="btn secondary" href="{{ route('careers.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
