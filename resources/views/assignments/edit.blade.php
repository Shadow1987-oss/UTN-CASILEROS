@extends('plantilla')

@section('titulo', 'Editar Asignación - UTN Lockers')

@section('contenido')
<div class="card">
    <h2>Editar asignación</h2>
    <form class="form" method="POST" action="{{ route('assignments.update', $assignment) }}" id="assignment-form">
        @csrf
        @method('PUT')
        <div class="field">
            <label for="matricula">Estudiante</label>
            <select id="matricula" name="matricula" class="input" required>
                <option value="">Seleccionar</option>
                @foreach ($students as $student)
                <option value="{{ $student->matricula }}" {{ old('matricula', $assignment->matricula) == $student->matricula ? 'selected' : '' }}>
                    {{ $student->nombre }} ({{ $student->matricula }})
                </option>
                @endforeach
            </select>
            @error('matricula') <div class="field-help error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="idcasillero">Casillero</label>
            <select id="idcasillero" name="idcasillero" class="input" required>
                <option value="">Seleccionar</option>
                @foreach ($lockers as $locker)
                <option value="{{ $locker->idcasillero }}" {{ old('idcasillero', $assignment->idcasillero) == $locker->idcasillero ? 'selected' : '' }}>
                    {{ $locker->numeroCasiller }} - {{ optional($locker->building)->num_edific ?? $locker->idedificio }}
                </option>
                @endforeach
            </select>
            @error('idcasillero') <div class="field-help error">{{ $message }}</div> @enderror
        </div>

        <div class="field">
            <label for="idPeriodo">Período</label>
            <select id="idPeriodo" name="idPeriodo" class="input" required>
                <option value="">Seleccionar</option>
                @foreach ($periods as $period)
                <option value="{{ $period->idperiodo }}" {{ old('idPeriodo', $assignment->idPeriodo) == $period->idperiodo ? 'selected' : '' }}>
                    {{ $period->nombrePerio }} ({{ $period->idperiodo }})
                </option>
                @endforeach
            </select>
            @error('idPeriodo') <div class="field-help error">{{ $message }}</div> @enderror
        </div>

        @include('partials.form-field', ['label' => 'Notas', 'name' => 'notes', 'type' => 'textarea', 'value' => old('notes', $assignment->notes)])

        <div class="actions">
            <button class="btn" type="submit">Actualizar</button>
            <a class="btn secondary" href="{{ route('assignments.index') }}">Cancelar</a>
        </div>
    </form>
</div>
@endsection
