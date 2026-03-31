@extends('plantilla')

@section('titulo', 'Editar Asignación - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Editar asignación</h2>
        <form class="form" method="POST" action="{{ route('assignments.update', $assignment) }}" id="assignment-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="idasigna" value="{{ old('idasigna', $assignment->idasigna) }}">
            <div class="field">
                <label for="matricula">Estudiante</label>
                <select id="matricula" name="matricula" class="input" required>
                    <option value="">Seleccionar</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->matricula }}"
                            {{ old('matricula', $assignment->matricula) == $student->matricula ? 'selected' : '' }}>
                            {{ $student->full_name }} ({{ $student->matricula_display }}) - Grupo
                            {{ $student->grupo ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('matricula')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="idcasillero">Casillero</label>
                <div class="grid grid-2" style="margin-bottom:8px;">
                    <select id="filterArea" class="input">
                        <option value="">Todas las áreas</option>
                        @foreach ($lockers->pluck('area')->filter()->unique()->sort()->values() as $area)
                            <option value="{{ $area }}">{{ $area }}</option>
                        @endforeach
                    </select>
                    <select id="filterPlanta" class="input">
                        <option value="">Todas las plantas</option>
                        <option value="baja">Planta baja</option>
                        <option value="alta">Planta alta</option>
                    </select>
                </div>
                <select id="idcasillero" name="idcasillero" class="input" required>
                    <option value="">Seleccionar</option>
                    @foreach ($lockers as $locker)
                        <option value="{{ $locker->idcasillero }}" data-area="{{ $locker->area }}"
                            data-planta="{{ $locker->planta }}"
                            {{ old('idcasillero', $assignment->idcasillero) == $locker->idcasillero ? 'selected' : '' }}>
                            {{ $locker->numeroCasiller }} -
                            {{ optional($locker->building)->num_edific ?? $locker->idedificio }}
                        </option>
                    @endforeach
                </select>
                @error('idcasillero')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="idPeriodo">Período</label>
                <select id="idPeriodo" name="idPeriodo" class="input" required>
                    <option value="">Seleccionar</option>
                    @foreach ($periods as $period)
                        <option value="{{ $period->idperiodo }}"
                            {{ old('idPeriodo', $assignment->idPeriodo) == $period->idperiodo ? 'selected' : '' }}>
                            {{ $period->nombrePerio }} ({{ $period->idperiodo }})
                        </option>
                    @endforeach
                </select>
                @error('idPeriodo')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="idusuario">Tutor responsable</label>
                <select id="idusuario" name="idusuario" class="input">
                    <option value="">Seleccionar tutor</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->idusuario }}"
                            {{ old('idusuario', $assignment->idusuario) == $usuario->idusuario ? 'selected' : '' }}>
                            {{ $usuario->nombre }} {{ $usuario->apellidoP }} ({{ $usuario->cargo }})
                            - Casilleros activos: {{ $tutorLoads[$usuario->idusuario] ?? 0 }}
                        </option>
                    @endforeach
                </select>
                @error('idusuario')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="fechaAsignacion">Fecha de asignación</label>
                <input type="date" id="fechaAsignacion" name="fechaAsignacion" class="input"
                    value="{{ old('fechaAsignacion', optional($assignment->fechaAsignacion)->format('Y-m-d')) }}" required>
                @error('fechaAsignacion')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>

            <div class="actions">
                <button class="btn" type="submit">Actualizar</button>
                <a class="btn secondary" href="{{ route('assignments.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        (function() {
            const areaFilter = document.getElementById('filterArea');
            const plantaFilter = document.getElementById('filterPlanta');
            const lockerSelect = document.getElementById('idcasillero');

            if (!areaFilter || !plantaFilter || !lockerSelect) {
                return;
            }

            const applyFilters = () => {
                const selectedArea = areaFilter.value;
                const selectedPlanta = plantaFilter.value;

                Array.from(lockerSelect.options).forEach((option, index) => {
                    if (index === 0) {
                        option.hidden = false;
                        return;
                    }

                    const matchArea = !selectedArea || option.dataset.area === selectedArea;
                    const matchPlanta = !selectedPlanta || option.dataset.planta === selectedPlanta;
                    const visible = matchArea && matchPlanta;

                    option.hidden = !visible;
                    if (!visible && option.selected) {
                        option.selected = false;
                    }
                });
            };

            areaFilter.addEventListener('change', applyFilters);
            plantaFilter.addEventListener('change', applyFilters);
            applyFilters();
        })();
    </script>
@endsection
