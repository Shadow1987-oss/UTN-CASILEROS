{{-- Formulario de edición de casillero. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Editar Casillero - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Editar casillero</h2>
        <form class="form" method="POST" action="{{ route('lockers.update', $locker) }}" id="locker-form">
            @csrf
            @method('PUT')
            <div class="field">
                <label for="idedificio">Edificio</label>
                <select id="idedificio" name="idedificio" class="input">
                    <option value="">Sin edificio</option>
                    @foreach ($buildings as $building)
                        <option value="{{ $building->idedificio }}"
                            {{ old('idedificio', $locker->idedificio) == $building->idedificio ? 'selected' : '' }}>
                            {{ $building->idedificio }} - {{ $building->num_edific }}
                        </option>
                    @endforeach
                </select>
                @error('idedificio')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="area">Área</label>
                <select id="area" name="area" class="input" required>
                    <option value="">Seleccionar</option>
                    @foreach (['Laboratorios', 'Aulas', 'Biblioteca', 'Administrativo'] as $area)
                        <option value="{{ $area }}" {{ old('area', $locker->area) == $area ? 'selected' : '' }}>
                            {{ $area }}</option>
                    @endforeach
                </select>
                @error('area')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="planta">Planta</label>
                <select id="planta" name="planta" class="input">
                    <option value="">Seleccionar</option>
                    <option value="baja" {{ old('planta', $locker->planta) == 'baja' ? 'selected' : '' }}>Planta baja
                    </option>
                    <option value="alta" {{ old('planta', $locker->planta) == 'alta' ? 'selected' : '' }}>Planta alta
                    </option>
                </select>
                @error('planta')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            @include('partials.form-field', [
                'label' => 'Número de casillero',
                'name' => 'numeroCasiller',
                'value' => old('numeroCasiller', $locker->numeroCasiller),
                'placeholder' => 'Ej. 101',
                'required' => true,
            ])
            <div class="field">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" class="input" required>
                    <option value="disponible" {{ old('estado', $locker->estado) == 'disponible' ? 'selected' : '' }}>
                        Disponible</option>
                    <option value="ocupado" {{ old('estado', $locker->estado) == 'ocupado' ? 'selected' : '' }}>Ocupado
                    </option>
                    <option value="dañado" {{ old('estado', $locker->estado) == 'dañado' ? 'selected' : '' }}>Dañado
                    </option>
                </select>
                @error('estado')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            @include('partials.form-field', [
                'label' => 'Observaciones',
                'name' => 'observaciones',
                'type' => 'textarea',
                'value' => old('observaciones', $locker->observaciones),
                'maxlength' => 255,
            ])
            <div class="actions">
                <button class="btn" type="submit">Actualizar</button>
                <a class="btn secondary" href="{{ route('lockers.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
