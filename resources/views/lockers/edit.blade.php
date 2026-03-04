@extends('plantilla')

@section('titulo', 'Editar Casillero - UTN Lockers')

@section('contenido')
<div class="card">
    <h2>Editar casillero</h2>
    <form class="form" method="POST" action="{{ route('lockers.update', $locker) }}" id="locker-form">
        @csrf
        @method('PUT')
        @include('partials.form-field', ['label' => 'Edificio (id)', 'name' => 'idedificio', 'value' => old('idedificio', $locker->idedificio), 'placeholder' => 'ID Edificio'])
        @include('partials.form-field', ['label' => 'Número de casillero', 'name' => 'numeroCasiller', 'value' => old('numeroCasiller', $locker->numeroCasiller), 'placeholder' => 'Ej. 101', 'required' => true])
        @include('partials.form-field', ['label' => 'Estado', 'name' => 'estado', 'value' => old('estado', $locker->estado), 'placeholder' => 'Ej. libre/ocupado', 'required' => true])
        <div class="actions">
            <button class="btn" type="submit">Actualizar</button>
            <a class="btn secondary" href="{{ route('lockers.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
