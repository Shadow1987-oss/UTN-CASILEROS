@extends('plantilla')
@section('titulo', 'Casilleros - UTN Lockers')
@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Casilleros</h2>
                <p class="muted">Administrar inventario de casilleros.</p>
            </div>
            @if (auth()->user()->role === 'admin')
                <a class="btn" href="{{ route('lockers.create') }}">Nuevo casillero</a>
            @endif
        </div>

        <form method="GET" action="{{ route('lockers.index') }}" class="form" style="margin-bottom: 20px;">
            <div class="grid grid-3">
                <div class="field">
                    <label for="idedificio">Edificio</label>
                    <select name="idedificio" id="idedificio" class="input">
                        <option value="">Todos</option>
                        @foreach ($buildings as $building)
                            <option value="{{ $building->idedificio }}"
                                {{ request('idedificio') == $building->idedificio ? 'selected' : '' }}>
                                {{ $building->num_edific }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="area">Área</label>
                    <select name="area" id="area" class="input">
                        <option value="">Todas</option>
                        @foreach (['Laboratorios', 'Aulas', 'Biblioteca', 'Administrativo'] as $area)
                            <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>
                                {{ $area }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field">
                    <label for="planta">Planta</label>
                    <select name="planta" id="planta" class="input">
                        <option value="">Todas</option>
                        <option value="baja" {{ request('planta') == 'baja' ? 'selected' : '' }}>Planta baja</option>
                        <option value="alta" {{ request('planta') == 'alta' ? 'selected' : '' }}>Planta alta</option>
                    </select>
                </div>
            </div>
            <div class="field">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" class="input">
                    <option value="">Todos</option>
                    <option value="disponible" {{ request('estado') == 'disponible' ? 'selected' : '' }}>Disponible
                    </option>
                    <option value="ocupado" {{ request('estado') == 'ocupado' ? 'selected' : '' }}>Ocupado</option>
                    <option value="dañado" {{ request('estado') == 'dañado' ? 'selected' : '' }}>Dañado</option>
                </select>
            </div>
            <div class="actions">
                <button type="submit" class="btn">Filtrar</button>
                <a href="{{ route('lockers.index') }}" class="btn secondary">Limpiar</a>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Edificio (id)</th>
                    <th>Área</th>
                    <th>Planta</th>
                    <th>Número</th>
                    <th>Estado</th>
                    <th>Observaciones</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lockers as $locker)
                    <tr>
                        <td>{{ $locker->idcasillero }}</td>
                        <td>{{ optional($locker->building)->num_edific ?? $locker->idedificio }}</td>
                        <td>{{ $locker->area ?? '-' }}</td>
                        <td>{{ $locker->planta ? ucfirst($locker->planta) : '-' }}</td>
                        <td>{{ $locker->numeroCasiller }}</td>
                        <td><span class="pill">{{ $locker->estado ?? '-' }}</span></td>
                        <td>{{ $locker->observaciones ?? '-' }}</td>
                        <td class="actions">
                            @if (auth()->user()->role === 'admin')
                                <a class="btn secondary" href="{{ route('lockers.edit', $locker) }}">Editar</a>
                                <form method="POST" action="{{ route('lockers.destroy', $locker) }}"
                                    onsubmit="return confirm('¿Eliminar este casillero?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn danger" type="submit">Eliminar</button>
                                </form>
                            @else
                                <span class="muted">Solo lectura</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">No hay casilleros registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 16px;">
            {{ $lockers->links() }}
        </div>
    </div>
@endsection
