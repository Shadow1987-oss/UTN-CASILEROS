@extends('plantilla')

@section('titulo', 'Nuevo Casillero - UTN Lockers')

@section('contenido')
<div class="card">
    <h2>Nuevo casillero</h2>
    <form class="form" method="POST" action="{{ route('lockers.store') }}" id="locker-form">
        @csrf
        @include('partials.form-field', ['label' => 'Edificio (id)', 'name' => 'idedificio', 'value' => old('idedificio'), 'placeholder' => 'ID Edificio'])
        @include('partials.form-field', ['label' => 'Número de casillero', 'name' => 'numeroCasiller', 'value' => old('numeroCasiller'), 'placeholder' => 'Ej. 101', 'required' => true])
        @include('partials.form-field', ['label' => 'Estado', 'name' => 'estado', 'value' => old('estado'), 'placeholder' => 'Ej. libre/ocupado', 'required' => true])
        <div class="actions">
            <button class="btn" type="submit">Guardar</button>
            <a class="btn secondary" href="{{ route('lockers.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
