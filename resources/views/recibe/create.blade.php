{{-- Formulario de creación de recibo vinculando sanción con estudiante. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Nuevo Recibo - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Nuevo recibo de sanción</h2>
        <form class="form" method="POST" action="{{ route('recibe.store') }}" id="recibo-form">
            @csrf
            @include('partials.form-field', [
                'label' => 'ID del recibo',
                'name' => 'idrecibe',
                'type' => 'text',
                'value' => old('idrecibe'),
                'required' => true,
                'placeholder' => 'Ej. 1, 2, 3…',
                'inputmode' => 'numeric',
            ])
            <div class="field">
                <label for="idsancion">Sanción</label>
                <select id="idsancion" name="idsancion" class="input" required>
                    <option value="">Seleccionar sanción</option>
                    @foreach ($sanciones as $sancion)
                        <option value="{{ $sancion->idsancion }}"
                            {{ old('idsancion') == $sancion->idsancion ? 'selected' : '' }}>
                            #{{ $sancion->idsancion }} - {{ $sancion->sancion }}
                        </option>
                    @endforeach
                </select>
                @error('idsancion')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="matricula">Estudiante</label>
                <select id="matricula" name="matricula" class="input" required>
                    <option value="">Seleccionar estudiante</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->matricula }}"
                            {{ old('matricula') == $student->matricula ? 'selected' : '' }}>
                            {{ $student->full_name }} ({{ $student->matricula_display }}) - Grupo
                            {{ $student->grupo ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('matricula')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            <div class="actions">
                <button class="btn" type="submit">Guardar</button>
                <a class="btn secondary" href="{{ route('recibe.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
