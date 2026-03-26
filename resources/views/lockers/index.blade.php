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

        <form method="GET" action="{{ route('lockers.index') }}" style="margin-bottom: 20px;">
            <div class="field">
                <label for="estado">Filtrar por estado</label>
                <select name="estado" id="estado">
                    <option value="">Todos</option>
                    <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="ocupado" {{ request('estado') == 'ocupado' ? 'selected' : '' }}>Ocupado</option>
                    <option value="dañado" {{ request('estado') == 'dañado' ? 'selected' : '' }}>Dañado</option>
                </select>
                <button type="submit" class="btn">Filtrar</button>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Edificio (id)</th>
                    <th>Número</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lockers as $locker)
                    <tr>
                        <td>{{ $locker->idcasillero }}</td>
                        <td>{{ optional($locker->building)->num_edific ?? $locker->idedificio }}</td>
                        <td>{{ $locker->numeroCasiller }}</td>
                        <td><span class="pill">{{ $locker->estado ?? '-' }}</span></td>
                        <td class="actions">
                            <a class="btn secondary" href="{{ route('lockers.edit', $locker) }}">Editar</a>
                            <form method="POST" action="{{ route('lockers.destroy', $locker) }}"
                                onsubmit="return confirm('¿Eliminar este casillero?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No hay casilleros registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
