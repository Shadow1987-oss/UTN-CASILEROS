@extends('plantilla')

@section('titulo', 'Editar Edificio - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Editar edificio</h2>
        <form class="form" method="POST" action="{{ route('buildings.update', $building) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="idedificio" value="{{ old('idedificio', $building->idedificio) }}">
            @include('partials.form-field', [
                'label' => 'Número/clave de edificio',
                'name' => 'num_edific',
                'value' => old('num_edific', $building->num_edific),
                'required' => true,
                'maxlength' => 50,
            ])
            <div class="actions">
                <button class="btn" type="submit">Actualizar</button>
                <a class="btn secondary" href="{{ route('buildings.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
