<?php

namespace App\Http\Controllers;

use App\Mail\FaltaUnoCancelledMail;
use App\Mail\FaltaUnoJoinedMail;
use App\Mail\FaltaUnoFullMail;
use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoParticipant;
use App\Models\Field;
use App\Models\Venue;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FaltaUnoController extends Controller
{
    public function __construct(private ReservationService $reservationService) {}

    /**
     * Lista global de todos los partidos abiertos en todos los complejos.
     */
    public function index(Request $request)
    {
        $sport = $request->query('sport');

        $games = FaltaUnoGame::with([
                'field.venue',
                'field.faltaUnoSetting',
                'activeParticipants.user',
                'reservation',
            ])
            ->whereIn('status', ['open', 'full'])
            ->whereHas('reservation', fn($q) => $q->where('status', 'PAID'))
            ->where('start_at', '>', now())
            ->when($sport, fn($q) => $q->whereHas('field', fn($q2) => $q2->where('sport', $sport)))
            ->orderBy('start_at')
            ->get();

        return view('falta-uno.index', compact('games', 'sport'));
    }

    /**
     * Muestra el formulario para iniciar un partido.
     */
    public function create(Request $request, Field $field)
    {
        $field->load(['venue', 'price', 'faltaUnoSetting']);

        if (!$field->faltaUnoSetting?->enabled) {
            abort(404);
        }

        return view('falta-uno.create', compact('field'));
    }

    /**
     * Guarda el partido y crea la reserva para el iniciador.
     */
    public function store(Request $request, Field $field)
    {
        $field->load(['venue', 'price', 'faltaUnoSetting']);

        if (!$field->faltaUnoSetting?->enabled) {
            abort(404);
        }

        $data = $request->validate([
            'start_at'          => ['required', 'date', 'after:now'],
            'total_players'     => ['required', 'integer', 'min:2', 'max:100'],
            'initiator_players' => ['required', 'integer', 'min:1'],
        ]);

        $totalPlayers     = (int) $data['total_players'];
        $initiatorPlayers = (int) $data['initiator_players'];

        if ($initiatorPlayers >= $totalPlayers) {
            return back()->withErrors(['initiator_players' => 'Los jugadores que traés deben ser menos que el total.'])->withInput();
        }

        $playersNeeded = $totalPlayers - $initiatorPlayers;
        $start         = Carbon::parse($data['start_at']);

        $user = $request->user();

        try {
            DB::beginTransaction();

            // Crear la reserva — el servicio ya calcula precio nocturno + descuentos
            $reservation = $this->reservationService->createSingle(
                $field,
                $start,
                $user->id,
                30, // expira en 30 minutos
            );

            // Aplicar proporción sobre el precio real calculado por el servicio
            $fullPrice   = (float) $reservation->total_amount;
            $amountToPay = round(($initiatorPlayers / $totalPlayers) * $fullPrice, 2);

            $reservation->update(['total_amount' => $amountToPay]);

            // Crear el partido falta uno
            $game = FaltaUnoGame::create([
                'field_id'          => $field->id,
                'reservation_id'    => $reservation->id,
                'initiator_user_id' => $user->id,
                'total_players'     => $totalPlayers,
                'initiator_players' => $initiatorPlayers,
                'players_needed'    => $playersNeeded,
                'status'            => 'open',
                'start_at'          => $start,
                'amount_paid'       => $amountToPay,
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }

        return redirect()->route('reservations.checkout', $reservation)
            ->with('info', 'Completá el pago para publicar tu partido.');
    }

    /**
     * Unirse a un partido.
     */
    public function join(Request $request, FaltaUnoGame $game)
    {
        $user = $request->user();

        if ($game->status !== 'open') {
            return back()->with('error', 'Este partido ya no está disponible.');
        }

        if ($game->reservation?->status !== 'PAID') {
            return back()->with('error', 'Este partido no está disponible aún.');
        }

        if ($game->isFull()) {
            return back()->with('error', 'El partido ya está completo.');
        }

        if ($game->initiator_user_id === $user->id) {
            return back()->with('error', 'Sos el iniciador del partido.');
        }

        $alreadyJoined = FaltaUnoParticipant::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->exists();

        if ($alreadyJoined) {
            return back()->with('error', 'Ya estás anotado en este partido.');
        }

        DB::transaction(function () use ($game, $user) {
            FaltaUnoParticipant::create([
                'game_id' => $game->id,
                'user_id' => $user->id,
                'status'  => 'confirmed',
            ]);

            // Notificar al iniciador
            $game->loadMissing('initiator');
            Mail::to($game->initiator->email)->send(new FaltaUnoJoinedMail($game, $user));

            // Si se completó el partido, actualizar status y notificar
            if ($game->isFull()) {
                $game->update(['status' => 'full']);
                Mail::to($game->initiator->email)->send(new FaltaUnoFullMail($game));
            }
        });

        return back()->with('success', '¡Te anotaste! Presentate en el complejo el día del partido.');
    }

    /**
     * Cancelar un partido (solo el iniciador).
     */
    public function cancel(Request $request, FaltaUnoGame $game)
    {
        $user = $request->user();

        if ($game->initiator_user_id !== $user->id) {
            abort(403);
        }

        if (!in_array($game->status, ['open', 'full'])) {
            return back()->with('error', 'Este partido no se puede cancelar.');
        }

        $canRefund = $game->canRefund();

        DB::transaction(function () use ($game, $canRefund) {
            // Si había participantes y el partido estaba lleno, avisarles
            $wasFullOrHadParticipants = $game->activeParticipants()->exists();
            if ($wasFullOrHadParticipants) {
                $game->load('activeParticipants.user', 'field.venue');
                foreach ($game->activeParticipants as $participant) {
                    Mail::to($participant->user->email)
                        ->send(new FaltaUnoCancelledMail($game, $participant->user));
                }
            }

            $game->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Cancelar la reserva
            if ($game->reservation) {
                $game->reservation->update(['status' => 'CANCELLED']);
            }
        });

        $msg = $canRefund
            ? 'Partido cancelado. Procesaremos el reembolso.'
            : 'Partido cancelado. No corresponde reembolso según la política del complejo.';

        return redirect()->route('venues.show', $game->field->venue_id)
            ->with('success', $msg);
    }
}
