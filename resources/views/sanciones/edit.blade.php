{{-- Formulario de edición de sanción. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Editar Sanción - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Editar sanción</h2>
        <form class="form" method="POST" action="{{ route('sanciones.update', $sancione) }}" id="sancion-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="idsancion" value="{{ old('idsancion', $sancione->idsancion) }}">
            <div class="field">
                <label for="idusuario">Tutor responsable</label>
                <select id="idusuario" name="idusuario" class="input">
                    <option value="">Seleccionar tutor</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->idusuario }}"
                            {{ old('idusuario', $sancione->idusuario) == $usuario->idusuario ? 'selected' : '' }}>
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
                            {{ old('matricula', $selectedMatricula) == $student->matricula ? 'selected' : '' }}>
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
                'value' => old('sancion', $sancione->sancion),
                'required' => true,
                'placeholder' => 'Ej. Daño al casillero',
                'maxlength' => 50,
            ])
            @include('partials.form-field', [
                'label' => 'Motivo',
                'name' => 'motivo',
                'value' => old('motivo', $sancione->motivo),
                'placeholder' => 'Ej. Cerradura rota',
                'maxlength' => 50,
            ])
            <div class="actions">
                <button class="btn" type="submit">Actualizar</button>
                <a class="btn secondary" href="{{ route('sanciones.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
