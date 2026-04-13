{{-- Listado de tutores/usuarios del dominio.
     Acciones: editar, eliminar. Acceso: admin. --}}
@extends('plantilla')

@section('titulo', 'Tutores - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Tutores</h2>
                <p class="muted">Catálogo de personal responsable.</p>
            </div>
            <a href="{{ route('usuarios.create') }}" class="btn">Nuevo tutor</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre completo</th>
                    <th>Cargo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->idusuario }}</td>
                        <td>{{ $usuario->nombre }} {{ $usuario->apellidoP ?? '' }} {{ $usuario->apellidoM ?? '' }}</td>
                        <td>{{ $usuario->cargo ?? '-' }}</td>
                        <td class="actions">
                            <a class="btn secondary" href="{{ route('usuarios.edit', $usuario) }}">Editar</a>
                            <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}" style="display:inline;"
                                onsubmit="return confirm('¿Eliminar este tutor?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">Sin tutores.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top: 16px;">
            {{ $usuarios->links() }}
        </div>
    </div>
@endsection
