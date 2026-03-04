@extends('plantilla')

@section('titulo', 'Nuevo Recibo - UTN Lockers')

@section('contenido')
<div class="card">
    <h2>Nuevo recibo</h2>
    <form method="POST" action="{{ route('recibe.store') }}">
        @csrf
        @include('partials.form-field', ['label' => 'Sanción (id)', 'name' => 'idsancion', 'value' => old('idsancion')])
        @include('partials.form-field', ['label' => 'Matrícula', 'name' => 'matricula', 'value' => old('matricula')])
        <div class="actions">
            <button class="btn" type="submit">Guardar</button>
            <a class="btn secondary" href="{{ route('recibe.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
