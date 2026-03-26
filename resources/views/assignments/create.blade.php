@extends('plantilla')

@section('titulo', 'Nueva Asignación - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Nueva asignación</h2>
        <form class="form" method="POST" action="{{ route('assignments.store') }}" id="assignment-form">
            @csrf
            @include('partials.form-field', [
                'label' => 'ID de asignación',
                'name' => 'idasigna',
                'type' => 'number',
                'value' => old('idasigna'),
                'required' => true,
            ])
            <div class="field">
                <label for="matricula">Estudiante</label>
                <select id="matricula" name="matricula" class="input" required>
                    <option value="">Seleccionar</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->matricula }}"
                            {{ old('matricula') == $student->matricula ? 'selected' : '' }}>
                            {{ $student->nombre }} ({{ $student->matricula }})
                        </option>
                    @endforeach
                </select>
                @error('matricula')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="idcasillero">Casillero</label>
                <select id="idcasillero" name="idcasillero" class="input" required>
                    <option value="">Seleccionar</option>
                    @foreach ($lockers as $locker)
                        <option value="{{ $locker->idcasillero }}"
                            {{ old('idcasillero') == $locker->idcasillero ? 'selected' : '' }}>
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
                            {{ old('idPeriodo') == $period->idperiodo ? 'selected' : '' }}>
                            {{ $period->nombrePerio }} ({{ $period->idperiodo }})
                        </option>
                    @endforeach
                </select>
                @error('idPeriodo')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label for="idusuario">Usuario responsable</label>
                <select id="idusuario" name="idusuario" class="input">
                    <option value="">Seleccionar usuario</option>
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
                <label for="fechaAsignacion">Fecha de asignación</label>
                <input type="date" id="fechaAsignacion" name="fechaAsignacion" class="input"
                    value="{{ old('fechaAsignacion', date('Y-m-d')) }}" required>
                @error('fechaAsignacion')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>



            <div class="actions">
                <button class="btn" type="submit">Guardar</button>
                <a class="btn secondary" href="{{ route('assignments.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
