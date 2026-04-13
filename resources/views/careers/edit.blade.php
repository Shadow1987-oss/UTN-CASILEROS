{{-- Formulario de edición de carrera. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Editar Carrera - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Editar carrera</h2>
        <form class="form" method="POST" action="{{ route('careers.update', $career) }}" id="career-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="idcarrera" value="{{ old('idcarrera', $career->idcarrera) }}">
            @include('partials.form-field', [
                'label' => 'Nombre de carrera',
                'name' => 'nombre_carre',
                'value' => old('nombre_carre', $career->nombre_carre),
                'required' => true,
                'maxlength' => 50,
            ])
            <div class="actions">
                <button class="btn" type="submit">Actualizar</button>
                <a class="btn secondary" href="{{ route('careers.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
