@extends('plantilla')

@section('titulo', 'Casilleros - UTN Lockers')

@section('contenido')
<div class="card">
    <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <div>
            <h2>Casilleros</h2>
            <p class="muted">Administrar inventario de casilleros.</p>
        </div>
        <a class="btn" href="{{ route('lockers.create') }}">Nuevo casillero</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Ubicación</th>
                <th>Estado</th>
                <th>Notas</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lockers as $locker)
            <tr>
                <td>{{ $locker->code }}</td>
                <td>{{ $locker->location }}</td>
                <td><span class="pill">{{ $locker->active ? 'Activo' : 'Inactivo' }}</span></td>
                <td>{{ $locker->notes ?? '-' }}</td>
                <td class="actions">
                    <a class="btn secondary" href="{{ route('lockers.edit', $locker) }}">Editar</a>
                    <form method="POST" action="{{ route('lockers.destroy', $locker) }}" onsubmit="return confirm('¿Eliminar este casillero?');" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn danger" type="submit">Eliminar</button>
                    </form>
                <tr>
                    <td>{{ $locker->numeroCasiller }}</td>
                    <td>{{ optional($locker->building)->num_edific ?? $locker->idedificio }}</td>
                    <td><span class="pill">{{ $locker->estado ?? '-' }}</span></td>
                    <td>-</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
