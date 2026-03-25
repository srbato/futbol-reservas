<?php

namespace App\Http\Controllers;

use App\Events\FaltaUnoParticipantJoined;
use App\Jobs\NotifyFaltaUnoCreated;
use App\Mail\FaltaUnoCancelledMail;
use App\Mail\FaltaUnoJoinedMail;
use App\Mail\FaltaUnoFullMail;
use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoParticipant;
use App\Models\Field;
use App\Models\Venue;
use App\Notifications\FaltaUnoCancelledNotification;
use App\Notifications\FaltaUnoGameFullNotification;
use App\Notifications\FaltaUnoPlayerJoinedNotification;
use App\Services\MercadoPagoRefundService;
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
        $sport    = $request->query('sport');
        $gender   = $request->query('gender');
        $category = $request->query('category');

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
            ->when($gender, fn($q) => $q->where(fn($q2) => $q2->where('gender_filter', $gender)->orWhere('gender_filter', 'mixed')))
            ->orderBy('start_at')
            ->get()
            ->when($category, fn($coll) => $coll->filter(fn($game) => $game->isInCategoryRange($category)));

        return view('falta-uno.index', compact('games', 'sport', 'gender', 'category'));
    }

    /**
     * Hub de detalle del partido.
     */
    public function show(FaltaUnoGame $game)
    {
        $game->load([
            'field.venue',
            'field.faltaUnoSetting',
            'activeParticipants.user',
            'initiator',
            'reservation',
            'ratings',
        ]);

        $userId      = auth()->id();
        $isInitiator = $userId && $game->initiator_user_id === $userId;
        $isJoined    = $userId && $game->activeParticipants->contains('user_id', $userId);
        $isParticipant = $isInitiator || $isJoined;

        $yaCalifico = $userId
            ? \App\Models\FaltaUnoRating::where('game_id', $game->id)->where('rater_user_id', $userId)->exists()
            : false;

        return view('falta-uno.show', compact('game', 'isInitiator', 'isJoined', 'isParticipant', 'yaCalifico'));
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
            'gender_filter'     => ['nullable', 'in:male,female,mixed'],
            'category_min'      => ['nullable', 'string'],
            'category_max'      => ['nullable', 'string'],
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
                'gender_filter'     => $data['gender_filter'] ?? 'mixed',
                'category_min'      => $data['category_min'] ?: null,
                'category_max'      => $data['category_max'] ?: null,
            ]);

            DB::commit();

            NotifyFaltaUnoCreated::dispatch($game);

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

        // Check sport profile
        $game->loadMissing('field');
        $sport   = $game->field->sport;
        $profile = $user->sportProfileFor($sport);

        if (!$profile) {
            return redirect('/profile#sport-profile')
                ->with('error', 'Completá tu perfil deportivo para poder unirte a partidos Falta Uno.');
        }

        if ($game->gender_filter !== 'mixed' && $profile->gender !== $game->gender_filter) {
            $genderLabel = $game->gender_filter === 'male' ? 'masculino' : 'femenino';
            return back()->with('error', "Este partido es solo para jugadores de género {$genderLabel}.");
        }

        if (($game->category_min || $game->category_max) && !$game->isInCategoryRange($profile->category)) {
            $range = ucfirst($game->category_min ?? 'cualquiera') . ' – ' . ucfirst($game->category_max ?? 'cualquiera');
            return back()->with('error', "Este partido acepta categorías {$range}. Tu categoría es {$profile->category}.");
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
            $game->initiator->notify(new FaltaUnoPlayerJoinedNotification($game, $user));

            // Si se completó el partido, actualizar status y notificar
            if ($game->isFull()) {
                $game->update(['status' => 'full']);
                Mail::to($game->initiator->email)->send(new FaltaUnoFullMail($game));
                $game->initiator->notify(new FaltaUnoGameFullNotification($game));
            }
        });

        $game->load('activeParticipants');
        broadcast(new FaltaUnoParticipantJoined($game));

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
        $game->loadMissing('reservation');
        $reservation = $game->reservation;

        // Intentar reembolso antes de la transacción para poder anotar el resultado en la reserva
        $refundProcessed = false;
        if (
            $canRefund
            && $reservation
            && $reservation->payment_provider === 'mercadopago'
            && $reservation->payment_external_id
        ) {
            $refundResult = app(MercadoPagoRefundService::class)->refund($reservation);

            if ($refundResult === true) {
                $refundProcessed = true;
            } elseif ($refundResult === false) {
                // El reembolso falló: anotar para gestión manual
                $reservation->notes = trim(($reservation->notes ?? '') . "\n[REEMBOLSO PENDIENTE] Falta Uno cancelado el " . now()->format('d/m/Y H:i') . '. Procesar manualmente.');
                $reservation->save();
            }
        }

        DB::transaction(function () use ($game, $reservation) {
            // Si había participantes, avisarles
            $wasFullOrHadParticipants = $game->activeParticipants()->exists();
            if ($wasFullOrHadParticipants) {
                $game->load('activeParticipants.user', 'field.venue');
                foreach ($game->activeParticipants as $participant) {
                    Mail::to($participant->user->email)
                        ->send(new FaltaUnoCancelledMail($game, $participant->user));
                    $participant->user->notify(new FaltaUnoCancelledNotification($game));
                }
            }

            $game->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Cancelar la reserva
            if ($reservation) {
                $reservation->update(['status' => 'CANCELLED']);
            }
        });

        if (!$canRefund) {
            $msg = 'Partido cancelado. No corresponde reembolso según la política del complejo.';
        } elseif ($refundProcessed) {
            $msg = 'Partido cancelado. El reembolso fue procesado correctamente.';
        } else {
            $msg = 'Partido cancelado. El reembolso será procesado manualmente a la brevedad.';
        }

        return redirect()->route('venues.show', $game->field->venue_id)
            ->with('success', $msg);
    }
}
