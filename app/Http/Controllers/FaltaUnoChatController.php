<?php

namespace App\Http\Controllers;

use App\Events\FaltaUnoChatMessageSent;
use App\Models\FaltaUnoGame;
use Illuminate\Http\Request;

class FaltaUnoChatController extends Controller
{
    public function index(FaltaUnoGame $game)
    {
        $user = auth()->user();

        $isParticipant = $game->initiator_user_id === $user->id
            || $game->participants()->where('user_id', $user->id)->where('status', 'confirmed')->exists();

        if (!$isParticipant) {
            abort(403, 'Solo los participantes del partido pueden acceder al chat.');
        }

        $messages = $game->messages()->with('user')->orderBy('created_at')->get();

        $game->loadMissing(['field.venue']);

        return view('falta-uno.chat', compact('game', 'messages'));
    }

    public function store(Request $request, FaltaUnoGame $game)
    {
        $user = auth()->user();

        $isParticipant = $game->initiator_user_id === $user->id
            || $game->participants()->where('user_id', $user->id)->where('status', 'confirmed')->exists();

        if (!$isParticipant) {
            abort(403);
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $message = $game->messages()->create([
            'user_id' => $user->id,
            'body'    => $data['body'],
        ]);

        $message->load('user');

        broadcast(new FaltaUnoChatMessageSent($message))->toOthers();

        return response()->json([
            'user' => [
                'id'     => $user->id,
                'name'   => $user->name,
                'avatar' => $user->avatar_path
                    ? \Illuminate\Support\Facades\Storage::url($user->avatar_path)
                    : null,
            ],
            'body'       => $message->body,
            'created_at' => $message->created_at->toISOString(),
        ]);
    }
}
