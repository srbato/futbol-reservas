<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Services\TournamentService;
use App\Services\TournamentFixtureService;
use Illuminate\Http\Request;

class TournamentOrganizerController extends Controller
{
    public function __construct(
        private TournamentService $tournamentService,
        private TournamentFixtureService $fixtureService,
    ) {}

    public function create()
    {
        $service = app(\App\Services\OrganizerSubscriptionService::class);
        [$canCreate, $message] = $service->canCreateTournament(auth()->user());

        if (!$canCreate) {
            return redirect()->route('organizador.planes')->with('info', $message);
        }

        $tier = $service->getTier(auth()->user());
        $maxTeams = $service->getMaxTeams(auth()->user());
        $formats = $service->getAvailableFormats(auth()->user());

        // Get all fields that accept tournaments, grouped by venue
        $availableVenues = \App\Models\Venue::whereHas('fields', function($q) {
            $q->whereHas('tournamentSetting', function($q2) {
                $q2->where('tournament_enabled', true);
            })->where('is_active', true);
        })->with(['fields' => function($q) {
            $q->whereHas('tournamentSetting', function($q2) {
                $q2->where('tournament_enabled', true);
            })->where('is_active', true)->with('tournamentSetting');
        }])->get();

        return view('torneos.create', compact('tier', 'maxTeams', 'formats', 'availableVenues'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'sport' => 'required|string|in:' . implode(',', Tournament::VALID_SPORTS),
            'max_teams' => 'required|integer|min:2|max:64',
            'players_per_team' => 'required|integer|min:1|max:30',
            'gender_filter' => 'required|in:male,female,mixed',
            'category' => 'nullable|string|max:50',
            'inscription_price' => 'nullable|numeric|min:0',
            'estimated_start_date' => 'required|date|after:today',
            'field_ids' => 'required|array|min:1',
            'field_ids.*' => 'exists:fields,id',
            'description' => 'nullable|string|max:2000',
            'rules' => 'nullable|string|max:5000',
            'cover_image' => 'nullable|image|max:5120',
        ]);

        $fieldIds = $data['field_ids'];
        unset($data['field_ids']);

        // Validate all fields: tournament_enabled + correct sport
        $fields = \App\Models\Field::with('venue', 'tournamentSetting')->whereIn('id', $fieldIds)->get();

        foreach ($fields as $field) {
            if (!$field->tournamentSetting || !$field->tournamentSetting->tournament_enabled) {
                return back()->withErrors(['field_ids' => "La cancha {$field->name} no acepta torneos."])->withInput();
            }
            if ($field->sport !== $data['sport']) {
                return back()->withErrors(['field_ids' => "La cancha {$field->name} es de otro deporte."])->withInput();
            }
        }

        // Use first venue's name as display name
        $firstField = $fields->first();
        $venueNames = $fields->pluck('venue.name')->unique()->join(', ');
        $data['external_venue_name'] = $venueNames;
        $data['external_venue_address'] = $firstField->venue->address;

        // Verificar limites del plan
        $service = app(\App\Services\OrganizerSubscriptionService::class);
        [$canCreate, $message] = $service->canCreateTournament(auth()->user());
        if (!$canCreate) {
            return redirect()->route('organizador.planes')->with('info', $message);
        }

        $maxAllowed = $service->getMaxTeams(auth()->user());
        if ($data['max_teams'] > $maxAllowed) {
            return back()->withErrors(['max_teams' => "Tu plan permite hasta {$maxAllowed} equipos."])->withInput();
        }

        // Forzar formato en Fase 1
        $data['format'] = 'single_elimination';

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('tournaments', 'public');
        }
        unset($data['cover_image']);

        $tournament = $this->tournamentService->createTournament(auth()->user(), $data);

        // Attach fields via pivot
        $tournament->fields()->attach($fieldIds);

        // Send venue requests for all fields
        $venueService = app(\App\Services\TournamentVenueService::class);
        $venueRequests = $venueService->sendMultipleVenueRequests($tournament, $fieldIds, 'Solicitud al crear torneo.');

        // Check results
        $tournament->refresh();
        $allApproved = collect($venueRequests)->every(fn($r) => $r->status === 'approved');

        if ($allApproved) {
            return redirect()->route('torneos.manage', $tournament)
                ->with('success', 'Torneo creado y todas las canchas aprobadas automaticamente. Publicalo cuando estes listo.');
        }

        // Build contact info message for pending requests
        $pendingRequests = collect($venueRequests)->filter(fn($r) => $r->status === 'pending');
        $contactMessages = [];

        foreach ($pendingRequests as $req) {
            $reqField = $fields->firstWhere('id', $req->field_id);
            if (!$reqField) continue;

            $setting = $reqField->tournamentSetting;
            $method = $setting->contact_method ?? 'internal';
            $value = $setting->contact_value ?? '';

            $msg = match($method) {
                'whatsapp' => $value ? "{$reqField->venue->name}: WhatsApp {$value}" : "{$reqField->venue->name}: WhatsApp",
                'phone' => $value ? "{$reqField->venue->name}: Tel {$value}" : "{$reqField->venue->name}: Telefono",
                'email' => $value ? "{$reqField->venue->name}: {$value}" : "{$reqField->venue->name}: Email",
                default => "{$reqField->venue->name}: por la plataforma",
            };
            $contactMessages[] = $msg;
        }

        $contactStr = implode(' | ', array_unique($contactMessages));

        return redirect()->route('torneos.manage', $tournament)
            ->with('info', "Torneo creado. Hay " . $pendingRequests->count() . " cancha(s) pendientes de aprobacion. Contacta: {$contactStr}");
    }

    public function edit(Tournament $tournament)
    {
        abort_if(!$tournament->isOrganizer(auth()->user()), 403);
        abort_if(in_array($tournament->status, [Tournament::STATUS_FINISHED, Tournament::STATUS_CANCELLED]), 403, 'No se puede editar un torneo finalizado o cancelado.');

        return view('torneos.edit', compact('tournament'));
    }

    public function update(Request $request, Tournament $tournament)
    {
        abort_if(!$tournament->isOrganizer(auth()->user()), 403);
        abort_if(in_array($tournament->status, [Tournament::STATUS_FINISHED, Tournament::STATUS_CANCELLED]), 403);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'max_teams' => 'required|integer|min:2|max:64',
            'players_per_team' => 'required|integer|min:1|max:30',
            'gender_filter' => 'required|in:male,female,mixed',
            'inscription_price' => 'nullable|numeric|min:0',
            'estimated_start_date' => 'required|date',
            'description' => 'nullable|string|max:2000',
            'rules' => 'nullable|string|max:5000',
            'cover_image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image_path'] = $request->file('cover_image')->store('tournaments', 'public');
        }
        unset($data['cover_image']);

        // No permitir cambiar max_teams si ya hay equipos inscriptos
        if ($tournament->confirmedTeams()->count() > 0) {
            unset($data['max_teams']);
        }

        $tournament->update($data);

        return redirect()->route('torneos.show', $tournament)->with('success', 'Torneo actualizado.');
    }

    public function manage(Tournament $tournament)
    {
        abort_if(!$tournament->isOrganizer(auth()->user()), 403);

        $tournament->load([
            'confirmedTeams.captain',
            'confirmedTeams.players',
            'matches.homeTeam',
            'matches.awayTeam',
            'matches.winner',
        ]);

        $rounds = $tournament->matches->groupBy('round')->sortKeys();
        $totalRounds = $rounds->count();

        // Load all venue requests with field/venue/setting info
        $venueRequestsData = [];
        $pendingVenueRequests = $tournament->venueRequests()
            ->with(['field.venue', 'field.tournamentSetting', 'messages.user'])
            ->get();

        foreach ($pendingVenueRequests as $req) {
            $setting = $req->field?->tournamentSetting;
            $contactMethod = $setting->contact_method ?? 'internal';
            $data = [
                'id' => $req->id,
                'field_name' => $req->field?->name ?? 'Cancha eliminada',
                'venue_name' => $req->field?->venue?->name ?? '',
                'status' => $req->status,
                'response_message' => $req->response_message,
                'contact_method' => $contactMethod,
                'contact_value' => $setting->contact_value ?? '',
            ];
            if ($contactMethod === 'internal') {
                $data['messages'] = $req->messages->map(fn($m) => [
                    'id' => $m->id,
                    'user_name' => $m->user?->name ?? 'Usuario',
                    'user_id' => $m->user_id,
                    'message' => $m->message,
                    'created_at' => $m->created_at->diffForHumans(),
                    'is_mine' => $m->user_id === auth()->id(),
                ])->toArray();
                $lastRead = $req->organizer_last_read_at;
                $data['unread_count'] = $req->messages
                    ->where('user_id', '!=', auth()->id())
                    ->when($lastRead, fn($c) => $c->where('created_at', '>', $lastRead))
                    ->count();
            }
            $venueRequestsData[] = $data;
        }

        return view('torneos.manage', compact('tournament', 'rounds', 'totalRounds', 'venueRequestsData'));
    }

    public function publish(Tournament $tournament)
    {
        abort_if(!$tournament->isOrganizer(auth()->user()), 403);

        $this->tournamentService->publishTournament($tournament);

        return redirect()->route('torneos.show', $tournament)->with('success', 'Torneo publicado. Ya pueden inscribirse equipos.');
    }

    public function closeRegistration(Tournament $tournament)
    {
        abort_if(!$tournament->isOrganizer(auth()->user()), 403);

        $this->tournamentService->closeRegistration($tournament);

        return redirect()->route('torneos.manage', $tournament)->with('success', 'Inscripcion cerrada.');
    }

    public function cancel(Request $request, Tournament $tournament)
    {
        abort_if(!$tournament->isOrganizer(auth()->user()), 403);

        $this->tournamentService->cancelTournament($tournament, $request->input('cancel_reason'));

        return redirect()->route('torneos.index')->with('success', 'Torneo cancelado.');
    }

    public function generateFixture(Tournament $tournament)
    {
        abort_if(!$tournament->isOrganizer(auth()->user()), 403);
        abort_if($tournament->status !== Tournament::STATUS_REGISTRATION_CLOSED, 422, 'Cerra la inscripcion antes de generar el fixture.');
        abort_if($tournament->confirmedTeams()->count() < 2, 422, 'Se necesitan al menos 2 equipos.');

        $this->fixtureService->generateSingleElimination($tournament);
        $this->tournamentService->startTournament($tournament);

        return redirect()->route('torneos.manage', $tournament)->with('success', 'Fixture generado. El torneo esta en curso.');
    }

    public function updateMatchResult(Request $request, Tournament $tournament, TournamentMatch $match)
    {
        abort_if(!$tournament->isOrganizer(auth()->user()), 403);
        abort_if($match->tournament_id !== $tournament->id, 404);
        abort_if($match->status === TournamentMatch::STATUS_FINISHED, 422, 'Este partido ya tiene resultado.');
        abort_if(!$match->home_team_id || !$match->away_team_id, 422, 'Este partido todavia no tiene equipos asignados.');

        $data = $request->validate([
            'home_score' => 'required|integer|min:0',
            'away_score' => 'required|integer|min:0',
            'home_penalties' => 'nullable|integer|min:0',
            'away_penalties' => 'nullable|integer|min:0',
        ]);

        $homeScore = (int) $data['home_score'];
        $awayScore = (int) $data['away_score'];
        $homePen = isset($data['home_penalties']) ? (int) $data['home_penalties'] : null;
        $awayPen = isset($data['away_penalties']) ? (int) $data['away_penalties'] : null;

        // Determinar ganador
        if ($homeScore !== $awayScore) {
            $winnerId = $homeScore > $awayScore ? $match->home_team_id : $match->away_team_id;
        } elseif ($homePen !== null && $awayPen !== null && $homePen !== $awayPen) {
            $winnerId = $homePen > $awayPen ? $match->home_team_id : $match->away_team_id;
        } else {
            return back()->withErrors(['home_score' => 'En eliminacion directa, si hay empate se necesitan penales con un ganador.']);
        }

        $match->update([
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'home_penalties' => $homePen,
            'away_penalties' => $awayPen,
            'winner_team_id' => $winnerId,
            'status' => TournamentMatch::STATUS_FINISHED,
            'played_at' => now(),
        ]);

        // Avanzar ganador en el bracket
        $this->fixtureService->advanceWinner($match);

        // Verificar si el torneo termino (la final tiene ganador)
        $finalMatch = $tournament->matches()->orderByDesc('round')->first();
        if ($finalMatch && $finalMatch->winner_team_id) {
            $this->tournamentService->finishTournament($tournament);
        }

        return redirect()->route('torneos.manage', $tournament)->with('success', 'Resultado cargado.');
    }

    public function searchVenue(Tournament $tournament)
    {
        abort_if(!$tournament->isOrganizer(auth()->user()), 403);

        $service = app(\App\Services\TournamentVenueService::class);
        $fields = $service->getAvailableFields($tournament->sport);

        return view('torneos.search-venue', compact('tournament', 'fields'));
    }

    public function requestVenue(Request $request, Tournament $tournament)
    {
        abort_if(!$tournament->isOrganizer(auth()->user()), 403);

        $data = $request->validate([
            'field_id' => 'required|exists:fields,id',
            'proposed_dates' => 'nullable|string|max:500',
            'message' => 'nullable|string|max:1000',
        ]);

        $field = \App\Models\Field::with('tournamentSetting')->findOrFail($data['field_id']);

        // Add to pivot if not already there
        if (!$tournament->fields()->where('field_id', $field->id)->exists()) {
            $tournament->fields()->attach($field->id);
        }

        $service = app(\App\Services\TournamentVenueService::class);
        $venueRequest = $service->sendVenueRequest($tournament, $field, [
            'proposed_dates' => $data['proposed_dates'] ? [$data['proposed_dates']] : null,
            'message' => $data['message'],
        ]);

        if ($venueRequest->status === 'approved') {
            return redirect()->route('torneos.manage', $tournament)
                ->with('success', 'Cancha aprobada automaticamente!');
        }

        $setting = $field->tournamentSetting;
        $contactMethod = $setting->contact_method ?? 'internal';
        $contactValue = $setting->contact_value ?? '';

        $contactMsg = match($contactMethod) {
            'whatsapp' => $contactValue ? "Contactalos por WhatsApp al {$contactValue}." : "Contactalos por WhatsApp.",
            'phone' => $contactValue ? "Llama al {$contactValue}." : "Contactalos por telefono.",
            'email' => $contactValue ? "Escribiles a {$contactValue}." : "Contactalos por email.",
            default => "El complejo la va a revisar desde la plataforma.",
        };

        return redirect()->route('torneos.manage', $tournament)
            ->with('info', "Solicitud enviada. {$contactMsg}");
    }
}
