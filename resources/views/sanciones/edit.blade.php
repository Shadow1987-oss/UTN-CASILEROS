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
                <label for="idusuario">Usuario responsable</label>
                <select id="idusuario" name="idusuario" class="input">
                    <option value="">Seleccionar usuario</option>
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
