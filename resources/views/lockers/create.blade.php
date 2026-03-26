@extends('plantilla')

@section('titulo', 'Nuevo Casillero - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Nuevo casillero</h2>
        <form class="form" method="POST" action="{{ route('lockers.store') }}" id="locker-form">
            @csrf
            @include('partials.form-field', [
                'label' => 'ID del casillero',
                'name' => 'idcasillero',
                'type' => 'number',
                'value' => old('idcasillero'),
                'required' => true,
            ])
            <div class="field">
                <label for="idedificio">Edificio</label>
                <select id="idedificio" name="idedificio" class="input">
                    <option value="">Sin edificio</option>
                    @foreach ($buildings as $building)
                        <option value="{{ $building->idedificio }}"
                            {{ old('idedificio') == $building->idedificio ? 'selected' : '' }}>
                            {{ $building->idedificio }} - {{ $building->num_edific }}
                        </option>
                    @endforeach
                </select>
                @error('idedificio')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            @include('partials.form-field', [
                'label' => 'Área',
                'name' => 'area',
                'value' => old('area'),
                'placeholder' => 'Ej. Laboratorios',
            ])
            <div class="field">
                <label for="planta">Planta</label>
                <select id="planta" name="planta" class="input">
                    <option value="">Seleccionar</option>
                    <option value="baja" {{ old('planta') == 'baja' ? 'selected' : '' }}>Planta baja</option>
                    <option value="alta" {{ old('planta') == 'alta' ? 'selected' : '' }}>Planta alta</option>
                </select>
                @error('planta')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            @include('partials.form-field', [
                'label' => 'Número de casillero',
                'name' => 'numeroCasiller',
                'value' => old('numeroCasiller'),
                'placeholder' => 'Ej. 101',
                'required' => true,
            ])
            <div class="field">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" class="input" required>
                    <option value="disponible" {{ old('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="ocupado" {{ old('estado') == 'ocupado' ? 'selected' : '' }}>Ocupado</option>
                    <option value="dañado" {{ old('estado') == 'dañado' ? 'selected' : '' }}>Dañado</option>
                </select>
                @error('estado')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            @include('partials.form-field', [
                'label' => 'Observaciones',
                'name' => 'observaciones',
                'type' => 'textarea',
                'value' => old('observaciones'),
                'maxlength' => 255,
            ])
            <div class="actions">
                <button class="btn" type="submit">Guardar</button>
                <a class="btn secondary" href="{{ route('lockers.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
