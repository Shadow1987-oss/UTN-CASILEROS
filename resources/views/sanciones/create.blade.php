@extends('plantilla')

@section('titulo', 'Nueva Sanción - UTN Lockers')

@section('contenido')
<div class="card">
    <h2>Nueva sanción</h2>
    <form method="POST" action="{{ route('sanciones.store') }}">
        @csrf
        @include('partials.form-field', ['label' => 'Usuario (id)', 'name' => 'idusuario', 'value' => old('idusuario')])
        @include('partials.form-field', ['label' => 'Sanción', 'name' => 'sancion', 'value' => old('sancion'), 'required' => true])
        @include('partials.form-field', ['label' => 'Motivo', 'name' => 'motivo', 'value' => old('motivo')])
        <div class="actions">
            <button class="btn" type="submit">Guardar</button>
            <a class="btn secondary" href="{{ route('sanciones.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
