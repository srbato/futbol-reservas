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

        // Prevent open redirect attacks:
        // - Reject protocol-relative URLs (starting with //)
        // - Reject any URL with scheme except the configured app URL
        // - Default to root on any failure
        $actionUrl = $this->sanitizeRedirectUrl($actionUrl);

        return redirect($actionUrl);
    }

    /**
     * Sanitize a URL to prevent open-redirect vulnerabilities.
     * Accepts only same-origin paths (starting with / but NOT //) or
     * absolute URLs under config('app.url').
     */
    private function sanitizeRedirectUrl(?string $url): string
    {
        if (!is_string($url) || $url === '') {
            return '/';
        }

        // Reject protocol-relative (e.g. //evil.com) and backslash tricks
        if (str_starts_with($url, '//') || str_starts_with($url, '\\\\') || str_starts_with($url, '\\/')) {
            return '/';
        }

        // Relative path: safe
        if (str_starts_with($url, '/')) {
            return $url;
        }

        // Absolute URL: must match the app host exactly
        $parsed = @parse_url($url);
        if (!is_array($parsed) || empty($parsed['host'])) {
            return '/';
        }

        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if (!$appHost || strtolower($parsed['host']) !== strtolower($appHost)) {
            return '/';
        }

        // Same host: reconstruct just path + query + fragment to strip any userinfo
        $path = $parsed['path'] ?? '/';
        $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
        return $path . $query . $fragment;
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }
}
