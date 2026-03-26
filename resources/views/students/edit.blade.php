@extends('plantilla')

@section('titulo', 'Editar Estudiante - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Editar estudiante</h2>
        <form class="form" method="POST" action="{{ route('students.update', $student) }}" id="student-form">
            @csrf
            @method('PUT')
            @include('partials.form-field', [
                'label' => 'Nombre(s)',
                'name' => 'nombre',
                'value' => old('nombre', $student->nombre),
                'placeholder' => 'Ej. Juan Carlos',
                'required' => true,
            ])
            @include('partials.form-field', [
                'label' => 'Matrícula',
                'name' => 'matricula',
                'value' => old('matricula', $student->matricula),
                'placeholder' => 'Ej. 320072',
                'required' => true,
            ])
            <div class="field">
                <label for="user_id">Cuenta de acceso (opcional)</label>
                <select id="user_id" name="user_id" class="input">
                    <option value="">Sin vincular</option>
                    @foreach ($studentUsers as $user)
                        <option value="{{ $user->id }}"
                            {{ old('user_id', $student->user_id) == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            <div class="field">
                <label for="idcarrera">Carrera</label>
                <select id="idcarrera" name="idcarrera" class="input">
                    <option value="">Seleccionar carrera</option>
                    @foreach ($careers as $career)
                        <option value="{{ $career->idcarrera }}"
                            {{ old('idcarrera', $student->idcarrera) == $career->idcarrera ? 'selected' : '' }}>
                            {{ $career->idcarrera }} - {{ $career->nombre_carre }}</option>
                    @endforeach
                </select>
                @error('idcarrera')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>
            @include('partials.form-field', [
                'label' => 'Cuatrimestre',
                'name' => 'cuatrimestre',
                'value' => old('cuatrimestre', $student->cuatrimestre),
                'placeholder' => 'Ej. 3',
            ])
            @include('partials.form-field', [
                'label' => 'Apellido Paterno',
                'name' => 'apellidoPaterno',
                'value' => old('apellidoPaterno', $student->apellidoPaterno),
                'placeholder' => 'Apellido paterno',
            ])
            @include('partials.form-field', [
                'label' => 'Apellido Materno',
                'name' => 'apellidoMaterno',
                'value' => old('apellidoMaterno', $student->apellidoMaterno),
                'placeholder' => 'Apellido materno',
            ])
            @include('partials.form-field', [
                'label' => 'Número de Teléfono',
                'name' => 'numero_telefonico',
                'value' => old('numero_telefonico', $student->numero_telefonico),
                'placeholder' => 'Ej. 3411234567',
            ])
            <div class="actions">
                <button class="btn" type="submit">Actualizar</button>
                <a class="btn secondary" href="{{ route('students.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
