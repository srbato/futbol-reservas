<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, string $id)
    {
        $notification = DatabaseNotification::findOrFail($id);

        // Solo el dueño puede marcar como leída
        if ($notification->notifiable_id !== $request->user()->id) {
            abort(403);
        }

        $notification->markAsRead();

        $actionUrl = $notification->data['action_url'] ?? '/';

        if (!str_starts_with($actionUrl, '/') && !str_starts_with($actionUrl, config('app.url'))) {
            $actionUrl = '/';
        }

        return redirect($actionUrl);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }
}
