@extends('plantilla')

@section('titulo', 'Períodos - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Períodos</h2>
                <p class="muted">Cuatrimestres y fechas.</p>
            </div>
            <a class="btn" href="{{ route('periods.create') }}">Nuevo período</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Fechas</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($periods as $period)
                    <tr>
                        <td>{{ $period->idperiodo }}</td>
                        <td>{{ $period->nombrePerio }}</td>
                        <td>{{ $period->fechaInicio ? \Carbon\Carbon::parse($period->fechaInicio)->format('Y-m-d') : '-' }}
                            a {{ $period->fechaFin ? \Carbon\Carbon::parse($period->fechaFin)->format('Y-m-d') : '-' }}</td>
                        <td class="actions">
                            <a class="btn secondary" href="{{ route('periods.edit', $period) }}">Editar</a>
                            <form method="POST" action="{{ route('periods.destroy', $period) }}"
                                onsubmit="return confirm('¿Eliminar este período?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="muted">Sin períodos aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
