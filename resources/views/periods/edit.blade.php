@extends('plantilla')

@section('titulo', 'Editar Período - UTN Lockers')

@section('contenido')
<div class="card">
    <h2>Editar período</h2>
    <form class="form" method="POST" action="{{ route('periods.update', $period) }}" id="period-form">
        @csrf
        @method('PUT')
        @include('partials.form-field', ['label' => 'Nombre del período', 'name' => 'nombrePerio', 'value' => old('nombrePerio', $period->nombrePerio), 'required' => true])
        @include('partials.form-field', ['label' => 'Fecha de inicio', 'name' => 'fechaInicio', 'type' => 'date', 'value' => old('fechaInicio', optional($period->fechaInicio)->format('Y-m-d'))])
        @include('partials.form-field', ['label' => 'Fecha de finalización', 'name' => 'fechaFin', 'type' => 'date', 'value' => old('fechaFin', optional($period->fechaFin)->format('Y-m-d'))])
        <div class="actions">
            <button class="btn" type="submit">Actualizar</button>
            <a class="btn secondary" href="{{ route('periods.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
