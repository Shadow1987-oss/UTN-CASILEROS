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
                            {{ $student->matricula_display }} - {{ $student->full_name }} ({{ $student->grupo ?? '-' }})
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
            @include('partials.form-field', [
                'label' => 'Observaciones',
                'name' => 'observaciones',
                'type' => 'textarea',
                'value' => old('observaciones', $report->observaciones),
                'required' => false,
                'maxlength' => 255,
            ])
            <div class="field">
                <label for="casilleros">Casilleros del alumno</label>
                <select id="casilleros" name="casilleros[]" class="input" multiple size="8">
                    @foreach ($lockers as $locker)
                        @php
                            $allowedStudents = $lockerStudentMap[$locker->idcasillero] ?? [];
                        @endphp
                        <option value="{{ $locker->idcasillero }}" data-students="{{ implode(',', $allowedStudents) }}"
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
                <div class="field-help" id="casilleros-help">Selecciona un alumno para ver sus casilleros activos. Si no
                    tiene casilleros, puedes guardar el reporte sin seleccionar.
                </div>
            </div>
            <div class="actions">
                <button class="btn" type="submit">Actualizar</button>
                <a class="btn secondary" href="{{ route('reportes.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        (function() {
            const studentSelect = document.getElementById('matricula');
            const lockersSelect = document.getElementById('casilleros');
            const help = document.getElementById('casilleros-help');

            if (!studentSelect || !lockersSelect) {
                return;
            }

            const normalizeMatricula = (value) => {
                const raw = String(value || '').toUpperCase().replace(/\s+/g, '');
                const match = raw.match(/^([A-Z]{2,10})-?(\d{3,10})$/);
                return match ? `${match[1]}-${match[2]}` : raw;
            };

            const filterLockersByStudent = () => {
                const selectedMatricula = normalizeMatricula(studentSelect.value);
                let visibleCount = 0;

                Array.from(lockersSelect.options).forEach((option) => {
                    const allowed = String(option.dataset.students || '')
                        .split(',')
                        .map((item) => item.trim())
                        .filter(Boolean);

                    const isAllowed = selectedMatricula !== '' && allowed.includes(selectedMatricula);
                    option.hidden = !isAllowed;

                    if (!isAllowed) {
                        option.selected = false;
                    } else {
                        visibleCount++;
                    }
                });

                if (selectedMatricula === '') {
                    help.textContent = 'Selecciona un alumno para ver únicamente sus casilleros activos.';
                    return;
                }

                if (visibleCount === 0) {
                    help.textContent = 'El alumno seleccionado no tiene casilleros activos reportables.';
                    return;
                }

                help.textContent =
                    `Casilleros activos disponibles: ${visibleCount}. Usa Ctrl/Cmd para seleccionar múltiples.`;
            };

            studentSelect.addEventListener('change', filterLockersByStudent);
            filterLockersByStudent();
        })();
    </script>
@endsection
