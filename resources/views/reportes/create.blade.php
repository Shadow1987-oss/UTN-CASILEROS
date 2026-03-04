@extends('plantilla')

@section('titulo', 'Nuevo Reporte - UTN Lockers')

@section('contenido')
<div class="card">
    <h2>Nuevo reporte</h2>
    <form method="POST" action="{{ route('reportes.store') }}">
        @csrf
        @include('partials.form-field', ['label' => 'Usuario (id)', 'name' => 'idusuario', 'value' => old('idusuario')])
        @include('partials.form-field', ['label' => 'Descripción', 'name' => 'descripcion', 'type' => 'textarea', 'value' => old('descripcion'), 'required' => true])
        <div class="actions">
            <button class="btn" type="submit">Guardar</button>
            <a class="btn secondary" href="{{ route('reportes.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
