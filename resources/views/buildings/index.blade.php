{{-- Listado de edificios con acciones CRUD. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Edificios - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Edificios</h2>
                <p class="muted">Catálogo de edificios.</p>
            </div>
            <a class="btn" href="{{ route('buildings.create') }}">Nuevo edificio</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Número/Clave</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($buildings as $building)
                    <tr>
                        <td>{{ $building->idedificio }}</td>
                        <td>{{ $building->num_edific }}</td>
                        <td class="actions">
                            <a class="btn secondary" href="{{ route('buildings.edit', $building) }}">Editar</a>
                            <form method="POST" action="{{ route('buildings.destroy', $building) }}" style="display:inline;"
                                onsubmit="return confirm('¿Eliminar este edificio?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="muted">Sin edificios registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
