<?php

namespace App\Http\Controllers;

use App\Events\FaltaUnoParticipantJoined;
use App\Jobs\NotifyFaltaUnoCreated;
use App\Mail\FaltaUnoCancelledMail;
use App\Mail\FaltaUnoJoinedMail;
use App\Mail\FaltaUnoFullMail;
use App\Mail\FaltaUnoLeftMail;
use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoParticipant;
use App\Models\FaltaUnoSportProfile;
use App\Models\Field;
use App\Models\User;
use App\Models\Venue;
use App\Notifications\FaltaUnoCancelledNotification;
use App\Notifications\FaltaUnoGameFullNotification;
use App\Notifications\FaltaUnoParticipantLeftNotification;
use App\Notifications\FaltaUnoPlayerJoinedNotification;
use App\Mail\FaltaUnoKickedMail;
use App\Notifications\FaltaUnoKickedNotification;
use App\Notifications\FaltaUnoNoShowNotification;
use App\Services\FaltaUnoPenaltyService;
use App\Services\MercadoPagoRefundService;
use App\Services\ReservationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FaltaUnoController extends Controller
{
    public function __construct(
        private ReservationService $reservationService,
        private FaltaUnoPenaltyService $penaltyService,
    ) {}

    /**
     * Lista global de todos los partidos abiertos en todos los complejos.
     */
    public function index(Request $request)
    {
        $sport    = $request->query('sport');
        $gender   = $request->query('gender');
        $category = $request->query('category');
        $zone     = $request->query('zone');

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
            ->when($zone, fn($q) => $q->whereHas('field.venue', fn($q2) => $q2->where('zone', $zone)))
            ->orderBy('start_at')
            ->get()
            ->when($category, fn($coll) => $coll->filter(fn($game) => $game->isInCategoryRange($category)));

        // Zonas disponibles: solo las de venues con partidos abiertos activos
        $zones = Venue::whereHas('fields.faltaUnoGames', function ($q) {
                $q->whereIn('status', ['open', 'full'])
                  ->whereHas('reservation', fn($r) => $r->where('status', 'PAID'))
                  ->where('start_at', '>', now());
            })
            ->whereNotNull('zone')
            ->where('zone', '!=', '')
            ->distinct()
            ->orderBy('zone')
            ->pluck('zone');

        // Canchas con Falta Uno habilitado (para el selector de "Crear partido")
        $faltaUnoFields = Field::with('venue')
            ->whereHas('faltaUnoSetting', fn($q) => $q->where('enabled', true))
            ->whereHas('venue', fn($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();

        return view('falta-uno.index', compact('games', 'sport', 'gender', 'category', 'zone', 'zones', 'faltaUnoFields'));
    }

    /**
     * Hub de detalle del partido.
     */
    public function show(FaltaUnoGame $game)
    {
        $game->load([
            'field.venue',
            'field.faltaUnoSetting',
            'activeParticipants.user.faltaUnoSportProfiles',
            'initiator.faltaUnoSportProfiles',
            'reservation',
            'ratings',
        ]);

        $userId      = auth()->id();
        $isInitiator = $userId && $game->initiator_user_id === $userId;
        $isJoined    = $userId && $game->activeParticipants->contains('user_id', $userId);
        $isParticipant = $isInitiator || $isJoined;

        // Si la reserva no esta pagada, solo el iniciador puede ver el partido
        if ($game->reservation && $game->reservation->status !== 'PAID' && !$isInitiator) {
            return redirect()->route('falta-uno.index')
                ->with('error', 'Este partido aun no esta disponible.');
        }

        $yaCalifico = $userId
            ? \App\Models\FaltaUnoRating::where('game_id', $game->id)->where('rater_user_id', $userId)->exists()
            : false;

        // Datos de reputacion para cada participante (para el organizador)
        $penaltyService = app(FaltaUnoPenaltyService::class);
        $sport = $game->field->sport;
        $reputationData = [];

        foreach ($game->activeParticipants as $p) {
            $reputationData[$p->user_id] = $penaltyService->getReputationData($p->user, $sport);
        }

        // Detectar si bajarse ahora seria tardia
        $deadlineMinutes = $game->field->faltaUnoSetting?->late_leave_deadline_minutes ?? 240;
        $wouldBeLateLeave = now()->gte($game->start_at->copy()->subMinutes($deadlineMinutes));

        // Verificar si el usuario puede unirse (penalidades)
        $joinCheck = $userId ? $penaltyService->canJoin(auth()->user()) : ['allowed' => true, 'reason' => null, 'warnings' => []];

        // Verificar si fue expulsado de este partido
        $wasKicked = $userId && FaltaUnoParticipant::where('game_id', $game->id)
            ->where('user_id', $userId)
            ->where('was_kicked', true)
            ->exists();

        // Verificar si fue marcado como no-show
        $wasNoShow = $userId && FaltaUnoParticipant::where('game_id', $game->id)
            ->where('user_id', $userId)
            ->where('status', 'no_show')
            ->exists();

        // Cargar participantes marcados como no-show
        $noShowParticipants = FaltaUnoParticipant::where('game_id', $game->id)
            ->where('status', 'no_show')
            ->with('user')
            ->get();

        // Cargar usuarios para calificar (si el partido terminó, no calificó aún, y no fue no-show)
        $otherUsers = collect();
        if ($userId && $game->isFinished() && !$yaCalifico && $isParticipant && !$wasNoShow) {
            $participantUserIds = $game->activeParticipants->pluck('user_id');
            $otherIds = collect([$game->initiator_user_id])
                ->merge($participantUserIds)
                ->unique()
                ->filter(fn($id) => $id !== $userId)
                ->values();
            $otherUsers = User::whereIn('id', $otherIds)->get();
        }

        return view('falta-uno.show', compact(
            'game', 'isInitiator', 'isJoined', 'isParticipant', 'yaCalifico',
            'reputationData', 'wouldBeLateLeave', 'joinCheck', 'wasKicked',
            'noShowParticipants', 'otherUsers', 'wasNoShow'
        ));
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
            'age_group_min'     => ['nullable', 'string', 'in:sub10,sub12,sub14,sub16,sub18,19a25,26a34,open,mas35,mas40,mas45,mas50,mas55,mas60'],
            'age_group_max'     => ['nullable', 'string', 'in:sub10,sub12,sub14,sub16,sub18,19a25,26a34,open,mas35,mas40,mas45,mas50,mas55,mas60'],
        ]);

        $totalPlayers     = (int) $data['total_players'];
        $initiatorPlayers = (int) $data['initiator_players'];

        if ($initiatorPlayers >= $totalPlayers) {
            return back()->withErrors(['initiator_players' => 'Los jugadores que traés deben ser menos que el total.'])->withInput();
        }

        // Validar que la categoría del iniciador esté dentro del rango definido para el partido
        $sport = $field->sport;
        if ($sport && ($data['category_min'] || $data['category_max'])) {
            $profile = $request->user()->sportProfileFor($sport);
            if ($profile) {
                $cats    = FaltaUnoSportProfile::getCategoriesForSport($sport);
                $userIdx = array_search($profile->category, $cats);
                if ($userIdx !== false) {
                    $minSearch = $data['category_min'] ? array_search($data['category_min'], $cats) : false;
                    $maxSearch = $data['category_max'] ? array_search($data['category_max'], $cats) : false;
                    $minIdx    = $minSearch !== false ? $minSearch : 0;
                    $maxIdx    = $maxSearch !== false ? $maxSearch : count($cats) - 1;
                    if ($userIdx < $minIdx || $userIdx > $maxIdx) {
                        $range = ucfirst($data['category_min'] ?? 'cualquiera') . ' – ' . ucfirst($data['category_max'] ?? 'cualquiera');
                        return back()->withErrors(['category_min' => "Tu categoría ({$profile->category}) está fuera del rango que definiste ({$range}). Solo podés crear partidos en los que tu categoría esté incluida."])->withInput();
                    }
                }
            }
        }

        // Validar que el grupo de edad del iniciador esté dentro del rango definido
        if ($data['age_group_min'] || $data['age_group_max']) {
            $userAgeGroup = $request->user()->age_group;
            if (!$userAgeGroup) {
                return redirect('/profile#personal-info')
                    ->with('error', 'Necesitás completar tu grupo de edad en tu perfil para crear un partido con filtro de edad.');
            }
            $ageOrder = FaltaUnoGame::getAgeGroupOrder();
            $userAgeIdx = array_search($userAgeGroup, $ageOrder);
            if ($userAgeIdx !== false) {
                $minAgeSearch = $data['age_group_min'] ? array_search($data['age_group_min'], $ageOrder) : false;
                $maxAgeSearch = $data['age_group_max'] ? array_search($data['age_group_max'], $ageOrder) : false;
                $minAgeIdx = $minAgeSearch !== false ? $minAgeSearch : 0;
                $maxAgeIdx = $maxAgeSearch !== false ? $maxAgeSearch : count($ageOrder) - 1;
                if ($userAgeIdx < $minAgeIdx || $userAgeIdx > $maxAgeIdx) {
                    $ageLabelMap = [
                        'sub10' => 'Sub 10', 'sub12' => 'Sub 12', 'sub14' => 'Sub 14',
                        'sub16' => 'Sub 16', 'sub18' => 'Sub 18', '19a25' => '19 a 25',
                        'mas35' => '+35', 'mas40' => '+40', 'mas45' => '+45', 'mas50' => '+50',
                        'mas55' => '+55', 'mas60' => '+60', '26a34' => '26 a 34', 'open' => 'Open',
                    ];
                    $range = ($ageLabelMap[$data['age_group_min']] ?? 'cualquiera') . ' – ' . ($ageLabelMap[$data['age_group_max']] ?? 'cualquiera');
                    $userAgeLabel = $ageLabelMap[$userAgeGroup] ?? $userAgeGroup;
                    return back()->withErrors(['age_group_min' => "Tu grupo de edad ({$userAgeLabel}) está fuera del rango que definiste ({$range}). Solo podés crear partidos en los que tu edad esté incluida."])->withInput();
                }
            }
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

            // Verificar que el precio calculado sea mayor a 0
            $fullPrice = (float) $reservation->total_amount;
            if ($fullPrice <= 0) {
                DB::rollBack();
                return back()->with('error', 'No se puede crear el partido: la cancha no tiene precio configurado.')->withInput();
            }

            // Aplicar proporcion sobre el precio real calculado por el servicio
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
                'age_group_min'     => $data['age_group_min'] ?: null,
                'age_group_max'     => $data['age_group_max'] ?: null,
            ]);

            DB::commit();

            NotifyFaltaUnoCreated::dispatch($game);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Error al crear partido Falta Uno', [
                'user_id'  => $user->id,
                'field_id' => $field->id,
                'start_at' => $data['start_at'] ?? 'N/A',
                'parsed'   => $start->toDateTimeString(),
                'error'    => $e->getMessage(),
            ]);
            return back()->with('error', 'Ocurrio un error al crear el partido. Por favor intenta nuevamente.')->withInput();
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

        // Verificar penalidades antes de todo
        $penaltyCheck = $this->penaltyService->canJoin($user);
        if (!$penaltyCheck['allowed']) {
            return back()->with('error', $penaltyCheck['reason']);
        }

        if ($game->status !== 'open') {
            return back()->with('error', 'Este partido ya no está disponible.');
        }

        if ($game->reservation?->status !== 'PAID') {
            return back()->with('error', 'Este partido no está disponible aún.');
        }

        if ($game->isFull()) {
            return back()->with('error', 'El partido ya está completo.');
        }

        if ($game->start_at->lte(now())) {
            return back()->with('error', 'Este partido ya comenzó. No podés unirte.');
        }

        $game->loadMissing('field.faltaUnoSetting');
        if ($game->hasPassedFillDeadline()) {
            return back()->with('error', 'El tiempo para unirse a este partido ya cerró.');
        }

        if ($game->initiator_user_id === $user->id) {
            return back()->with('error', 'Sos el iniciador del partido.');
        }

        // Check sport profile
        $game->loadMissing('field');
        $sport   = $game->field->sport;
        $profile = $user->sportProfileFor($sport);

        if (!$profile) {
            $sportLabels = ['football' => 'futbol', 'padel' => 'padel', 'tennis' => 'tenis', 'basketball' => 'basquet', 'volleyball' => 'voley'];
            $sportName = $sportLabels[$sport] ?? $sport;
            return redirect('/profile#sport-profile')
                ->with('error', "Necesitas un perfil deportivo de {$sportName} para unirte a este partido. Crealo desde tu perfil.");
        }

        if ($game->gender_filter !== 'mixed' && $profile->gender !== $game->gender_filter) {
            $genderLabel = $game->gender_filter === 'male' ? 'masculino' : 'femenino';
            return back()->with('error', "Este partido es solo para jugadores de género {$genderLabel}.");
        }

        if (($game->category_min || $game->category_max) && !$game->isInCategoryRange($profile->category)) {
            $range = ucfirst($game->category_min ?? 'cualquiera') . ' – ' . ucfirst($game->category_max ?? 'cualquiera');
            return back()->with('error', "Este partido acepta categorías {$range}. Tu categoría es {$profile->category}.");
        }

        // Validar grupo de edad
        if ($game->age_group_min || $game->age_group_max) {
            if (!$user->age_group) {
                return redirect('/profile#personal-info')
                    ->with('error', 'Necesitás completar tu grupo de edad en tu perfil para unirte a este partido.');
            }
            if (!$game->isInAgeGroupRange($user->age_group)) {
                $ageLabels = FaltaUnoGame::getAgeGroupOrder();
                $ageLabelMap = [
                    'sub10' => 'Sub 10', 'sub12' => 'Sub 12', 'sub14' => 'Sub 14',
                    'sub16' => 'Sub 16', 'sub18' => 'Sub 18', '19a25' => '19 a 25',
                    '26a34' => '26 a 34', 'open'  => 'Open',   'mas35' => '+35',
                    'mas40' => '+40',     'mas45' => '+45',     'mas50' => '+50',
                    'mas55' => '+55',     'mas60' => '+60',
                ];
                $minLabel = $ageLabelMap[$game->age_group_min] ?? 'cualquiera';
                $maxLabel = $ageLabelMap[$game->age_group_max] ?? 'cualquiera';
                $userLabel = $ageLabelMap[$user->age_group] ?? $user->age_group;
                return back()->with('error', "Este partido acepta edades {$minLabel} – {$maxLabel}. Tu grupo de edad es {$userLabel}.");
            }
        }

        $wasKicked = FaltaUnoParticipant::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->where('was_kicked', true)
            ->exists();

        if ($wasKicked) {
            return back()->with('error', 'Fuiste removido de este partido por el organizador. No podés volver a unirte.');
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
            if ($game->initiator && $game->initiator->email) {
                Mail::to($game->initiator->email)->send(new FaltaUnoJoinedMail($game, $user));
                $game->initiator->notify(new FaltaUnoPlayerJoinedNotification($game, $user));
            }

            // Si se completó el partido, actualizar status y notificar
            if ($game->isFull()) {
                $game->update(['status' => 'full']);
                if ($game->initiator && $game->initiator->email) {
                    Mail::to($game->initiator->email)->send(new FaltaUnoFullMail($game));
                    $game->initiator->notify(new FaltaUnoGameFullNotification($game));
                }
            }
        });

        $game->load('activeParticipants');
        broadcast(new FaltaUnoParticipantJoined($game));

        return back()->with('success', '¡Te anotaste! Presentate en el complejo el día del partido.');
    }

    /**
     * Salirse de un partido (solo participantes no iniciadores).
     */
    public function leave(Request $request, FaltaUnoGame $game)
    {
        $user = $request->user();

        if ($game->initiator_user_id === $user->id) {
            return back()->with('error', 'Sos el organizador del partido. Para cancelarlo usá la opción "Cancelar partido".');
        }

        if (!in_array($game->status, ['open', 'full'])) {
            return back()->with('error', 'No podés salirte de este partido.');
        }

        if ($game->start_at->lte(now())) {
            return back()->with('error', 'El partido ya comenzó. No podés salirte.');
        }

        $participant = FaltaUnoParticipant::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->first();

        if (!$participant) {
            return back()->with('error', 'No estás anotado en este partido.');
        }

        // Detectar si es bajada tardia
        $game->loadMissing('field.faltaUnoSetting');
        $deadlineMinutes = $game->field->faltaUnoSetting?->late_leave_deadline_minutes ?? 240;
        $isLateLeave = now()->gte($game->start_at->copy()->subMinutes($deadlineMinutes));

        DB::transaction(function () use ($game, $user, $participant) {
            $participant->update([
                'status'  => 'cancelled',
                'left_at' => now(),
            ]);

            // Si el partido estaba full, volver a open
            if ($game->status === 'full') {
                $game->update(['status' => 'open']);
            }

            // Notificar al iniciador
            $game->loadMissing('initiator', 'field.venue');
            if ($game->initiator && $game->initiator->email) {
                Mail::to($game->initiator->email)->send(new FaltaUnoLeftMail($game, $user));
                $game->initiator->notify(new FaltaUnoParticipantLeftNotification($game, $user));
            }
        });

        // Aplicar penalizacion si es bajada tardia (fuera de la transaccion para no bloquear el leave)
        if ($isLateLeave) {
            $this->penaltyService->registerLateLeave($user, $game);
            return back()->with('warning', 'Te saliste del partido. Esta bajada fue tardia y se aplico una penalizacion a tu cuenta.');
        }

        return back()->with('success', 'Te saliste del partido correctamente.');
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

        // Cargar participantes antes de la transaccion para notificar despues
        $participantsToNotify = collect();
        if ($game->activeParticipants()->exists()) {
            $game->load('activeParticipants.user', 'field.venue');
            $participantsToNotify = $game->activeParticipants->map(fn($p) => $p->user)->filter();
        }

        DB::transaction(function () use ($game, $reservation) {
            $game->update([
                'status'       => 'cancelled',
                'cancelled_at' => now(),
            ]);

            // Cancelar la reserva
            if ($reservation) {
                $reservation->update(['status' => 'CANCELLED']);
            }
        });

        // Notificar participantes fuera de la transaccion
        foreach ($participantsToNotify as $participantUser) {
            Mail::to($participantUser->email)
                ->send(new FaltaUnoCancelledMail($game, $participantUser));
            $participantUser->notify(new FaltaUnoCancelledNotification($game));
        }

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

    /**
     * El organizador expulsa a un participante del partido.
     */
    public function kick(Request $request, FaltaUnoGame $game, User $user)
    {
        $authUser = $request->user();

        // Solo el organizador puede expulsar
        if ($game->initiator_user_id !== $authUser->id) {
            abort(403);
        }

        // No puede expulsarse a si mismo
        if ($user->id === $authUser->id) {
            return back()->with('error', 'No podes expulsarte a vos mismo.');
        }

        if (!in_array($game->status, ['open', 'full'])) {
            return back()->with('error', 'No se puede expulsar jugadores de este partido.');
        }

        $participant = FaltaUnoParticipant::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->first();

        if (!$participant) {
            return back()->with('error', 'Este jugador no esta anotado en el partido.');
        }

        DB::transaction(function () use ($game, $user, $participant) {
            $participant->update(['status' => 'cancelled', 'is_late_leave' => false, 'was_kicked' => true]);

            // Si el partido estaba full, volver a open
            if ($game->status === 'full') {
                $game->update(['status' => 'open']);
            }
        });

        // Notificar al jugador expulsado (fuera de la transaccion)
        $game->loadMissing('field.venue');
        Mail::to($user->email)->send(new FaltaUnoKickedMail($game, $user));
        $user->notify(new FaltaUnoKickedNotification($game));

        return back()->with('success', $user->name . ' fue removido del partido.');
    }

    /**
     * El organizador marca no-shows despues de que el partido termino.
     */
    public function markNoShows(Request $request, FaltaUnoGame $game)
    {
        $authUser = $request->user();

        // Solo el organizador puede marcar no-shows
        if ($game->initiator_user_id !== $authUser->id) {
            abort(403);
        }

        // Solo si el partido ya empezo y esta en status full o played
        if (!in_array($game->status, ['full', 'played'])) {
            return back()->with('error', 'No se pueden marcar no-shows en este partido.');
        }

        if ($game->start_at->gt(now())) {
            return back()->with('error', 'El partido todavia no empezo.');
        }

        $data = $request->validate([
            'no_show_user_ids'   => ['nullable', 'array'],
            'no_show_user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $noShowUserIds = $data['no_show_user_ids'] ?? [];

        if (empty($noShowUserIds)) {
            return back()->with('info', 'No se selecciono ningun jugador como ausente.');
        }

        $game->loadMissing('field');
        $markedCount = 0;

        foreach ($noShowUserIds as $userId) {
            $participant = FaltaUnoParticipant::where('game_id', $game->id)
                ->where('user_id', $userId)
                ->where('status', 'confirmed')
                ->first();

            if (!$participant) {
                continue;
            }

            $participant->update([
                'status'     => 'no_show',
                'no_show_at' => now(),
            ]);

            // Aplicar penalizacion
            $this->penaltyService->registerNoShow($participant->user, $game);

            // Notificar al jugador
            $participant->user->notify(new FaltaUnoNoShowNotification($game));

            $markedCount++;
        }

        if ($markedCount === 0) {
            return back()->with('info', 'Ningun jugador valido fue marcado como ausente.');
        }

        return back()->with('success', "Se marcaron {$markedCount} jugador(es) como ausente(s).");
    }
}
