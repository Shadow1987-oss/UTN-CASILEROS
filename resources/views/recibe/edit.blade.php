@extends('plantilla')

@section('titulo', 'Editar Recibo - UTN Lockers')

@section('contenido')
    <div class="card">
        <h2>Editar recibo de sanción</h2>
        <form method="POST" action="{{ route('recibe.update', $recibo) }}">
            @csrf
            @method('PUT')

            <input type="hidden" name="idrecibe" value="{{ old('idrecibe', $recibo->idrecibe) }}">

            <div class="field">
                <label for="idsancion">Sanción</label>
                <select id="idsancion" name="idsancion" class="input" required>
                    <option value="">Seleccionar sanción</option>
                    @foreach ($sanciones as $sancion)
                        <option value="{{ $sancion->idsancion }}"
                            {{ old('idsancion', $recibo->idsancion) == $sancion->idsancion ? 'selected' : '' }}>
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
                            {{ old('matricula', $recibo->matricula) == $student->matricula ? 'selected' : '' }}>
                            {{ $student->nombre }} {{ $student->apellidoPaterno ?? '' }} ({{ $student->matricula }})
                        </option>
                    @endforeach
                </select>
                @error('matricula')
                    <div class="field-help error">{{ $message }}</div>
                @enderror
            </div>

            <div class="actions">
                <button class="btn" type="submit">Actualizar</button>
                <a class="btn secondary" href="{{ route('recibe.index') }}">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
