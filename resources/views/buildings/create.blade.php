{{-- Formulario de creación de edificio. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Nuevo Edificio - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Nuevo edificio</h2>
        <form class="form" method="POST" action="{{ route('buildings.store') }}">
            @csrf
            @include('partials.form-field', [
                'label' => 'ID de edificio',
                'name' => 'idedificio',
                'type' => 'text',
                'value' => old('idedificio'),
                'required' => true,
                'placeholder' => 'Ej. 1, 2, 3…',
                'inputmode' => 'numeric',
            ])
            @include('partials.form-field', [
                'label' => 'Número/clave de edificio',
                'name' => 'num_edific',
                'value' => old('num_edific'),
                'required' => true,
                'maxlength' => 50,
            ])
            <div class="actions">
                <button class="btn" type="submit">Guardar</button>
                <a class="btn secondary" href="{{ route('buildings.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
