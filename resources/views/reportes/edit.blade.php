@extends('plantilla')

@section('titulo', 'Editar Reporte - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Editar reporte</h2>
        <form method="POST" action="{{ route('reportes.update', $report) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="idreporte" value="{{ old('idreporte', $report->idreporte) }}">
            <div class="field">
                <label for="idusuario">Tutor responsable</label>
                <select id="idusuario" name="idusuario" class="input" required>
                    <option value="">Seleccionar tutor</option>
                    @foreach ($tutors as $tutor)
                        <option value="{{ $tutor->id }}"
                            {{ old('idusuario', $report->idusuario) == $tutor->id ? 'selected' : '' }}>
                            {{ $tutor->name }} ({{ $tutor->email }})
                        </option>
                    @endforeach
                </select>
                @error('idusuario')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="matricula">Alumno</label>
                <select id="matricula" name="matricula" class="input" required>
                    <option value="">Seleccionar alumno</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->matricula }}"
                            {{ old('matricula', $report->matricula) == $student->matricula ? 'selected' : '' }}>
                            {{ $student->matricula }} - {{ $student->nombre }} {{ $student->apellidoPaterno }}
                            {{ $student->apellidoMaterno }}
                        </option>
                    @endforeach
                </select>
                @error('matricula')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            @include('partials.form-field', [
                'label' => 'Descripción',
                'name' => 'descripcion',
                'type' => 'textarea',
                'value' => old('descripcion', $report->descripcion),
                'required' => true,
                'maxlength' => 50,
            ])
            <div class="field">
                <label for="casilleros">Casilleros del alumno</label>
                <select id="casilleros" name="casilleros[]" class="input" multiple size="8" required>
                    @foreach ($lockers as $locker)
                        <option value="{{ $locker->idcasillero }}"
                            {{ collect(old('casilleros', $selectedLockers))->contains($locker->idcasillero) ? 'selected' : '' }}>
                            #{{ $locker->numeroCasiller }}
                            @if ($locker->building)
                                - Edif. {{ $locker->building->num_edific }}
                            @endif
                            ({{ ucfirst($locker->estado) }})
                        </option>
                    @endforeach
                </select>
                @error('casilleros')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
                @error('casilleros.*')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
                <div class="field-help">Selecciona solo casilleros activos asignados al alumno. Usa Ctrl/Cmd para múltiples.
                </div>
            </div>
            <div class="actions">
                <button class="btn" type="submit">Actualizar</button>
                <a class="btn secondary" href="{{ route('reportes.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
