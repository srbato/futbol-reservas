<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        // Honeypot: si este campo tiene valor, es un bot
        if ($request->filled('website_url')) {
            return back()->with('feedback_sent', true);
        }

        $validated = $request->validate([
            'feedback_message' => ['required', 'string', 'min:10', 'max:2000'],
            'feedback_email'   => ['nullable', 'email', 'max:255'],
        ]);

        $email = $validated['feedback_email'] ?? null;

        // Si el usuario está logueado, usar su email
        if (auth()->check()) {
            $email = auth()->user()->email;
        }

        $message = $validated['feedback_message'];

        Mail::to('tucancha10@gmail.com')->queue(new FeedbackMail($message, $email));

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('feedback_sent', true);
    }
}
