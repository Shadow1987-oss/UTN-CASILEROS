{{-- Formulario de creación de sanción.
     Selecciona tutor, estudiante, tipo de sanción y motivo.
     Crea automáticamente el recibo asociado. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Nueva Sanción - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Nueva sanción</h2>
        <form method="POST" action="{{ route('sanciones.store') }}">
            @csrf
            @include('partials.form-field', [
                'label' => 'ID de sanción',
                'name' => 'idsancion',
                'type' => 'text',
                'value' => old('idsancion'),
                'required' => true,
                'placeholder' => 'Ej. 1, 2, 3…',
                'inputmode' => 'numeric',
            ])
            <div class="field">
                <label for="idusuario">Tutor responsable</label>
                <select id="idusuario" name="idusuario" class="input">
                    <option value="">Seleccionar tutor</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->idusuario }}"
                            {{ old('idusuario') == $usuario->idusuario ? 'selected' : '' }}>
                            {{ $usuario->nombre }} {{ $usuario->apellidoP }} ({{ $usuario->cargo }})
                        </option>
                    @endforeach
                </select>
                @error('idusuario')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="matricula">Estudiante sancionado</label>
                <select id="matricula" name="matricula" class="input" required>
                    <option value="">Seleccionar estudiante</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->matricula }}"
                            {{ old('matricula') == $student->matricula ? 'selected' : '' }}>
                            {{ $student->matricula_display }} - {{ $student->full_name }} ({{ $student->grupo ?? '-' }})
                        </option>
                    @endforeach
                </select>
                @error('matricula')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            @include('partials.form-field', [
                'label' => 'Sanción',
                'name' => 'sancion',
                'value' => old('sancion'),
                'required' => true,
                'placeholder' => 'Ej. Daño al casillero',
            ])
            @include('partials.form-field', [
                'label' => 'Motivo',
                'name' => 'motivo',
                'value' => old('motivo'),
                'placeholder' => 'Ej. Cerradura rota',
            ])
            <div class="actions">
                <button class="btn" type="submit">Guardar</button>
                <a class="btn secondary" href="{{ route('sanciones.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
