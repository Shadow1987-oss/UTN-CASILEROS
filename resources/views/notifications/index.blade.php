@extends('plantilla')

@section('titulo', 'Notificaciones - UTN Lockers')

@section('contenido')
    <div class="card">
        <div class="actions" style="justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <div>
                <h2>Notificaciones</h2>
                <p class="muted">Avisos de solicitudes y asignaciones.</p>
            </div>
            <form method="POST" action="{{ route('notifications.read_all') }}">
                @csrf
                <button class="btn secondary" type="submit">Marcar todas como leídas</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Título</th>
                    <th>Mensaje</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($notifications as $notification)
                    <tr>
                        <td>{{ optional($notification->created_at)->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $notification->title }}</td>
                        <td>{{ $notification->message }}</td>
                        <td>
                            <span class="pill">{{ $notification->read_at ? 'Leída' : 'Nueva' }}</span>
                        </td>
                        <td class="actions">
                            @if (!$notification->read_at)
                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                    @csrf
                                    <button class="btn" type="submit">Marcar leída</button>
                                </form>
                            @else
                                <span class="muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="muted">No tienes notificaciones.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 16px;">
            {{ $notifications->links() }}
        </div>
    </div>
@endsection
