<?php

use Illuminate\Support\Facades\Broadcast;

// Canal por defecto de Laravel para broadcasting notifications (notifiable->notify()
// con ShouldBroadcast). No sacar, lo usa el framework automáticamente.
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('falta-uno.{gameId}', function ($user, $gameId) {
    $game = \App\Models\FaltaUnoGame::find($gameId);
    if (!$game) {
        return false;
    }
    return $game->initiator_user_id === $user->id
        || $game->participants()->where('user_id', $user->id)->where('status', 'confirmed')->exists();
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('tournament-request.{requestId}', function ($user, $requestId) {
    $req = \App\Models\TournamentVenueRequest::find($requestId);
    if (!$req) return false;
    // Organizer or venue admin can listen
    $isOrganizer = $req->tournament && $req->tournament->isOrganizer($user);
    $isVenueAdmin = \App\Models\Venue::accessibleBy($user)->where('id', $req->venue_id)->exists();
    return $isOrganizer || $isVenueAdmin;
});
