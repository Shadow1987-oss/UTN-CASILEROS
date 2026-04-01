<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;

/**
 * Controlador de notificaciones internas del usuario.
 *
 * Muestra las notificaciones del usuario autenticado y permite
 * marcarlas como leídas (individual o masivamente).
 *
 * Tabla: user_notifications
 */
class NotificationController extends Controller
{
    /**
     * Listado paginado de notificaciones del usuario autenticado.
     *
     * Ordena primero las no leídas y luego por fecha descendente.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $notifications = UserNotification::where('user_id', auth()->id())
            ->orderByRaw('read_at IS NULL DESC')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Marca una notificación individual como leída.
     *
     * Solo el propietario puede marcarla; de lo contrario aborta 403.
     *
     * @param  \App\Models\UserNotification  $notification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markRead(UserNotification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        if (is_null($notification->read_at)) {
            $notification->update(['read_at' => now()]);
        }

        return redirect()->route('notifications.index')->with('status', 'Notificación marcada como leída.');
    }

    /**
     * Marca todas las notificaciones del usuario como leídas.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAllRead()
    {
        UserNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('notifications.index')->with('status', 'Todas las notificaciones fueron marcadas como leídas.');
    }
}
