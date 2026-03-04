@extends('plantilla')

@section('titulo', 'Nuevo Período - UTN Lockers')

@section('contenido')
<div class="card">
    <h2>Nuevo período</h2>
    <form class="form" method="POST" action="{{ route('periods.store') }}" id="period-form">
        @csrf
        @include('partials.form-field', ['label' => 'Nombre del período', 'name' => 'nombrePerio', 'value' => old('nombrePerio'), 'required' => true])
        @include('partials.form-field', ['label' => 'Fecha de inicio', 'name' => 'fechaInicio', 'type' => 'date', 'value' => old('fechaInicio')])
        @include('partials.form-field', ['label' => 'Fecha de finalización', 'name' => 'fechaFin', 'type' => 'date', 'value' => old('fechaFin')])
        <div class="actions">
            <button class="btn" type="submit">Guardar</button>
            <a class="btn secondary" href="{{ route('periods.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
