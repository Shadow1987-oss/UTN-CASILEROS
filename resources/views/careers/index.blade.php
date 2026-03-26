@extends('plantilla')

@section('titulo', 'Carreras - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Carreras</h2>
                <p class="muted">Catálogo académico.</p>
            </div>
            <a class="btn" href="{{ route('careers.create') }}">Nueva carrera</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($careers as $career)
                    <tr>
                        <td>{{ $career->idcarrera }}</td>
                        <td>{{ $career->nombre_carre }}</td>
                        <td class="actions">
                            <a class="btn secondary" href="{{ route('careers.edit', $career) }}">Editar</a>
                            <form method="POST" action="{{ route('careers.destroy', $career) }}" style="display:inline;"
                                onsubmit="return confirm('¿Eliminar esta carrera?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="muted">Sin carreras registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
