@extends('plantilla')

@section('titulo', 'Editar Sanción - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Editar sanción</h2>
        <form method="POST" action="{{ route('sanciones.update', $sancione) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="idsancion" value="{{ old('idsancion', $sancione->idsancion) }}">
            <div class="field">
                <label for="idusuario">Tutor responsable</label>
                <select id="idusuario" name="idusuario" class="input" disabled>
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
                <div class="field-help">El tutor de esta sanción no se puede modificar.</div>
            </div>
            <div class="field">
                <label for="matricula">Estudiante sancionado</label>
                <select id="matricula" name="matricula" class="input" required disabled>
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
                <div class="field-help">El estudiante sancionado no se puede modificar.</div>
            </div>
            <input type="hidden" name="idusuario" value="{{ old('idusuario', $sancione->idusuario) }}">
            <input type="hidden" name="matricula" value="{{ old('matricula', $selectedMatricula) }}">
            @include('partials.form-field', [
                'label' => 'Sanción',
                'name' => 'sancion',
                'value' => old('sancion', $sancione->sancion),
                'required' => true,
                'placeholder' => 'Ej. Daño al casillero',
            ])
            @include('partials.form-field', [
                'label' => 'Motivo',
                'name' => 'motivo',
                'value' => old('motivo', $sancione->motivo),
                'placeholder' => 'Ej. Cerradura rota',
            ])
            <div class="actions">
                <button class="btn" type="submit">Actualizar</button>
                <a class="btn secondary" href="{{ route('sanciones.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
