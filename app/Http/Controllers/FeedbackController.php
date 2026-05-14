<?php

namespace App\Http\Controllers;

use App\Mail\FeedbackMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        // Respuesta "exitosa" falsa para no darle pistas al bot de que fue detectado.
        $fakeOk = fn () => $request->expectsJson()
            ? response()->json(['ok' => true])
            : back()->with('feedback_sent', true);

        // ── Antibot 1: honeypot — campo invisible que sólo un bot completa
        if ($request->filled('website_url')) {
            return $fakeOk();
        }

        // ── Antibot 2: timing — un humano tarda más de 3s en escribir y enviar.
        // form_loaded_at es un timestamp (ms) que el front setea al abrir el panel.
        $loadedAt = (int) $request->input('form_loaded_at', 0);
        if ($loadedAt > 0) {
            $elapsedMs = (now()->valueOf()) - $loadedAt;
            if ($elapsedMs < 3000 || $elapsedMs > 3 * 60 * 60 * 1000) {
                // Demasiado rápido (bot) o demasiado viejo (token reutilizado)
                return $fakeOk();
            }
        }

        $validated = $request->validate([
            'feedback_message' => ['required', 'string', 'min:10', 'max:2000'],
            'feedback_email'   => ['nullable', 'email', 'max:255'],
        ]);

        $message = trim($validated['feedback_message']);

        // ── Antibot 3: filtro de contenido spam
        if ($this->looksLikeSpam($message)) {
            Log::info('Feedback descartado por filtro antispam', [
                'ip'      => $request->ip(),
                'preview' => mb_substr($message, 0, 80),
            ]);
            return $fakeOk();
        }

        $email = $validated['feedback_email'] ?? null;

        // Si el usuario está logueado, usar su email
        if (auth()->check()) {
            $email = auth()->user()->email;
        }

        Mail::to('tucancha10@gmail.com')->queue(new FeedbackMail($message, $email));

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('feedback_sent', true);
    }

    /**
     * Heurística simple antispam. El feedback legítimo de TuCancha viene en
     * español/inglés y rara vez con links. El spam de bots suele tener:
     *  - varios enlaces
     *  - mayoría de caracteres no latinos (cirílico, CJK, árabe, etc.)
     */
    private function looksLikeSpam(string $message): bool
    {
        // 3+ URLs → spam casi seguro
        $urlCount = preg_match_all('~https?://|www\.~i', $message);
        if ($urlCount >= 3) {
            return true;
        }

        // 2+ URLs en un mensaje corto
        if ($urlCount >= 2 && mb_strlen($message) < 200) {
            return true;
        }

        // Ratio de caracteres latinos: si menos del 50% del texto (sin contar
        // espacios/puntuación) es latino, asumimos spam en otro alfabeto.
        $letters = preg_replace('/[^\p{L}]/u', '', $message);
        $total   = mb_strlen($letters);
        if ($total >= 8) {
            $latin = preg_match_all('/[\p{Latin}]/u', $letters);
            if (($latin / $total) < 0.5) {
                return true;
            }
        }

        return false;
    }
}
