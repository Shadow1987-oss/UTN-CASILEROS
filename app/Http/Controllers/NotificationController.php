<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = UserNotification::where('user_id', auth()->id())
            ->orderByRaw('read_at IS NULL DESC')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('notifications.index', compact('notifications'));
    }

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

    public function markAllRead()
    {
        UserNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('notifications.index')->with('status', 'Todas las notificaciones fueron marcadas como leídas.');
    }
}
