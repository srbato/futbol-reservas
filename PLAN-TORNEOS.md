# PLAN TÉCNICO: Sistema de Torneos — TuCancha
## Versión 1.0 — Draft para revisión

---

## CONCEPTO GENERAL

Cualquier usuario puede crear un torneo. El torneo se vincula a un complejo que debe aprobar la solicitud. El complejo configura previamente si acepta torneos, en qué condiciones, y cómo quiere ser contactado. Una vez aprobado, las canchas se reservan automáticamente y los equipos se inscriben pagando via MercadoPago. El pago se divide entre complejo (cancha) y organizador (margen).

---

## FLUJO COMPLETO

### Fase 1: Configuración del complejo (venue admin)
1. El dueño del complejo habilita "Acepto torneos" por cancha
2. Configura:
   - Precio especial por partido de torneo (puede ser distinto al precio normal)
   - Horarios disponibles para torneos (ej: solo fines de semana)
   - Método de contacto preferido (WhatsApp, teléfono, chat interno, email)
   - Requisitos mínimos (cantidad mínima de equipos, depósito previo, etc.)
   - Si requiere aprobación manual o acepta automáticamente

### Fase 2: Creación del torneo (usuario organizador)
1. El usuario va a /torneos/crear
2. Configura el torneo:
   - Nombre del torneo
   - Deporte
   - Formato: eliminación directa, liga (todos contra todos), grupos + eliminación
   - Cantidad de equipos (4, 8, 16, 32)
   - Jugadores por equipo
   - Género (masculino, femenino, mixto)
   - Categoría (recreativo, intermedio, avanzado, competitivo / primera-octava en pádel)
   - Precio de inscripción por equipo
   - Fecha de inicio estimada
   - Reglas/descripción
3. Busca complejos que acepten torneos para ese deporte
4. Selecciona complejo y cancha
5. Propone fechas/horarios para los partidos
6. Envía solicitud al complejo

### Fase 3: Aprobación del complejo
1. El dueño recibe notificación (push + email)
2. Ve el detalle: organizador, torneo, fechas, cantidad de partidos
3. Puede:
   - Aprobar → se bloquean las canchas/horarios
   - Rechazar (con motivo)
   - Proponer cambios (sugiere otros horarios)
4. Al aprobar, el torneo pasa a estado OPEN_REGISTRATION

### Fase 4: Inscripción de equipos
1. El torneo se publica en /torneos (listado público)
2. Cualquier usuario puede crear un equipo e inscribirlo
3. Inscribir equipo:
   - Nombre del equipo
   - Logo/escudo (opcional)
   - Capitán (el que inscribe)
   - Jugadores (invitar por email o username)
4. Pago de inscripción via MercadoPago
   - Split: parte para el complejo (cancha) + parte para el organizador
5. Cuando se completan todos los equipos → se genera el fixture

### Fase 5: Torneo en curso
1. El fixture se genera automáticamente según el formato
2. Cada partido del fixture tiene:
   - Equipos enfrentados
   - Fecha y hora
   - Cancha asignada
   - Estado: pendiente, en curso, finalizado, suspendido
3. El organizador carga resultados de cada partido
4. Se actualiza automáticamente:
   - Tabla de posiciones (liga/grupos)
   - Bracket (eliminación)
   - Goleadores / estadísticas
5. Notificaciones a equipos: próximo partido, resultados, cambios

### Fase 6: Finalización
1. Se determina el campeón según el formato
2. Se genera resumen del torneo (stats, goleadores, MVP)
3. Se notifica a todos los participantes
4. El torneo pasa a estado FINISHED
5. Queda visible como historial

---

## MODELOS Y MIGRACIONES

### tournament_settings (config por cancha)
```
id
field_id (FK → fields, unique)
tournament_enabled (boolean, default: false)
tournament_price_per_match (decimal 12,2, nullable — precio especial, si null usa el normal)
available_days (JSON, nullable — ej: [0,6] para sáb/dom, null = todos)
available_start_time (time, nullable)
available_end_time (time, nullable)
contact_method (enum: 'whatsapp', 'phone', 'email', 'internal')
contact_value (string, nullable — número de WA, teléfono, email)
auto_approve (boolean, default: false)
min_teams (int, default: 4)
requires_deposit (boolean, default: false)
deposit_amount (decimal 12,2, nullable)
payment_mode (enum: 'upfront', 'per_match', 'monthly', 'split')
  — upfront: el organizador paga todo el costo de cancha antes de arrancar
  — per_match: se cobra antes de cada partido
  — monthly: se cobra mensualmente (para torneos largos tipo liga)
  — split: se descuenta automáticamente de cada inscripción de equipo
payment_due_days (int, default: 3 — días de anticipación para cobrar en modo per_match/monthly)
notes (text, nullable — condiciones especiales)
timestamps
```

### tournaments
```
id
organizer_user_id (FK → users)
field_id (FK → fields)
venue_id (FK → venues)
name (string)
description (text, nullable)
sport (string — football, padel, tennis, basketball, volleyball)
format (enum: 'single_elimination', 'double_elimination', 'round_robin', 'groups_elimination')
max_teams (int)
players_per_team (int)
gender_filter (enum: 'male', 'female', 'mixed')
category_min (string, nullable)
category_max (string, nullable)
inscription_price (decimal 12,2 — precio por equipo)
currency (string, default: 'ARS')
venue_price_per_match (decimal 12,2 — lo que cobra el complejo por partido)
platform_fee_percent (decimal 5,2, default: 8.00 — comisión de TuCancha en %)
organizer_margin_per_team (decimal 12,2 — lo que se queda el organizador por equipo, calculado automáticamente)
estimated_start_date (date)
actual_start_date (date, nullable)
rules (text, nullable)
status (enum: 'draft', 'pending_venue', 'venue_rejected', 'open_registration', 'registration_closed', 'in_progress', 'finished', 'cancelled')
venue_rejection_reason (text, nullable)
venue_approved_at (datetime, nullable)
venue_approved_by (FK → users, nullable)
registration_deadline (datetime, nullable)
cover_image_path (string, nullable)
groups_count (int, nullable — para formato grupos+eliminación)
teams_per_group (int, nullable)
advancing_per_group (int, nullable — cuántos avanzan de cada grupo)
cancelled_at (datetime, nullable)
cancel_reason (text, nullable)
timestamps
```

### tournament_teams
```
id
tournament_id (FK → tournaments)
name (string)
logo_path (string, nullable)
captain_user_id (FK → users)
status (enum: 'pending_payment', 'confirmed', 'disqualified', 'withdrawn')
seed (int, nullable — para seeding del bracket)
group_number (int, nullable — en qué grupo está)
inscription_paid_at (datetime, nullable)
payment_external_id (string, nullable)
payment_provider (string, nullable)
mp_preference_id (string, nullable)
timestamps

unique: [tournament_id, name]
```

### tournament_team_players
```
id
team_id (FK → tournament_teams)
user_id (FK → users, nullable — puede ser un jugador no registrado)
name (string — nombre del jugador, por si no tiene cuenta)
email (string, nullable)
role (enum: 'captain', 'player', 'substitute')
jersey_number (int, nullable)
status (enum: 'confirmed', 'pending', 'removed')
invited_at (datetime, nullable)
confirmed_at (datetime, nullable)
timestamps

unique: [team_id, user_id] where user_id is not null
```

### tournament_matches
```
id
tournament_id (FK → tournaments)
round (int — ronda del torneo: 1, 2, 3...)
round_name (string, nullable — "Cuartos de final", "Semifinal", "Final", "Fecha 1")
group_number (int, nullable — para fase de grupos)
match_number (int — orden dentro de la ronda)
home_team_id (FK → tournament_teams, nullable — null si es TBD)
away_team_id (FK → tournament_teams, nullable)
field_id (FK → fields)
reservation_id (FK → reservations, nullable — la reserva real de la cancha)
scheduled_at (datetime)
status (enum: 'scheduled', 'in_progress', 'finished', 'suspended', 'cancelled', 'walkover')
home_score (int, nullable)
away_score (int, nullable)
home_penalties (int, nullable — para definición por penales)
away_penalties (int, nullable)
winner_team_id (FK → tournament_teams, nullable)
notes (text, nullable)
played_at (datetime, nullable)
timestamps

index: [tournament_id, round, match_number]
```

### tournament_standings (tabla de posiciones para liga/grupos)
```
id
tournament_id (FK → tournaments)
team_id (FK → tournament_teams)
group_number (int, nullable)
played (int, default: 0)
won (int, default: 0)
drawn (int, default: 0)
lost (int, default: 0)
goals_for (int, default: 0)
goals_against (int, default: 0)
goal_difference (int, default: 0)
points (int, default: 0)
position (int, nullable — se calcula)
timestamps

unique: [tournament_id, team_id]
```

### tournament_match_events (goles, tarjetas, etc.)
```
id
match_id (FK → tournament_matches)
team_id (FK → tournament_teams)
player_id (FK → tournament_team_players, nullable)
event_type (enum: 'goal', 'own_goal', 'yellow_card', 'red_card', 'substitution', 'penalty_scored', 'penalty_missed')
minute (int, nullable)
notes (string, nullable)
timestamps
```

### tournament_venue_requests (solicitudes al complejo)
```
id
tournament_id (FK → tournaments)
venue_id (FK → venues)
requested_by (FK → users)
proposed_dates (JSON — array de fechas/horarios propuestos)
message (text, nullable — mensaje del organizador)
status (enum: 'pending', 'approved', 'rejected', 'counter_proposed')
response_message (text, nullable)
counter_proposed_dates (JSON, nullable)
responded_at (datetime, nullable)
responded_by (FK → users, nullable)
timestamps
```

### tournament_settlements (liquidaciones por pago de inscripción)
```
id
tournament_id (FK → tournaments)
team_id (FK → tournament_teams)
total_paid (decimal 12,2 — lo que pagó el equipo)
venue_amount (decimal 12,2 — parte para el complejo)
platform_amount (decimal 12,2 — parte para TuCancha)
organizer_amount (decimal 12,2 — parte para el organizador)
payment_external_id (string, nullable)
venue_settled (boolean, default: false)
organizer_settled (boolean, default: false)
settled_at (datetime, nullable)
timestamps
```

---

## ECONOMÍA DEL TORNEO

### Cómo se calcula el split por equipo

```
inscription_price = lo que paga cada equipo (lo pone el organizador)
venue_cost_per_team = (venue_price_per_match * total_matches) / max_teams
platform_fee = inscription_price * (platform_fee_percent / 100)
organizer_margin = inscription_price - venue_cost_per_team - platform_fee
```

### Ejemplo: Torneo de Fútbol 5, eliminación directa, 8 equipos
```
- Inscripción por equipo: $10.000
- Complejo cobra $5.000 por partido
- Total partidos: 7 (cuartos: 4, semi: 2, final: 1)
- Costo total cancha: 7 × $5.000 = $35.000
- Costo cancha por equipo: $35.000 / 8 = $4.375
- Comisión TuCancha (8%): $10.000 × 0.08 = $800
- Margen organizador: $10.000 - $4.375 - $800 = $4.825

Recaudación total: 8 × $10.000 = $80.000
  → Complejo: $35.000 (43.75%)
  → TuCancha: $6.400 (8%)
  → Organizador: $38.600 (48.25%)
```

### Validaciones de precio
- El sistema calcula automáticamente si el `inscription_price` propuesto por el organizador cubre al menos el costo de cancha + comisión de TuCancha
- Si no cubre, no permite crear el torneo (el organizador estaría perdiendo plata)
- Se muestra un desglose transparente al organizador antes de publicar

### Momento del cobro a los equipos
- El equipo paga la inscripción al inscribirse (siempre)
- TuCancha retiene su comisión inmediatamente

### Momento del cobro al complejo (según payment_mode del complejo)

**upfront (todo ya):**
- El organizador paga el costo total de cancha al complejo antes de que arranque el torneo
- Si no paga, el torneo no arranca
- El complejo recibe la plata antes del primer partido

**per_match (por partido):**
- Se cobra automáticamente X días antes de cada partido (configurable con payment_due_days)
- Se descuenta del pool de inscripciones retenido
- Si no hay fondos suficientes, se notifica al organizador

**monthly (mensual):**
- Para torneos largos (ligas de varios meses)
- Se genera una factura mensual al organizador con los partidos del mes
- Se descuenta del pool o se cobra aparte

**split (automático por inscripción):**
- Cada vez que un equipo paga la inscripción, la parte del complejo se transfiere automáticamente
- Es el modo más simple: no requiere intervención
- El complejo va cobrando a medida que los equipos se inscriben

### Liquidación al organizador
- Se liquida cuando el torneo termina (en todos los modos)
- O parcialmente, si el torneo es largo, se puede liquidar por fase/mes

### Cancelación y reembolso
- Si se cancela antes de arrancar: reembolso a los equipos menos comisión TuCancha
- Si se cancela en curso: reembolso proporcional a equipos por partidos no jugados
- Al complejo: se le paga solo los partidos que efectivamente se jugaron

---

## CONTROLADORES

### Público
- `TournamentController` — index (listar torneos), show (detalle), search
- `TournamentTeamController` — create (inscribir equipo), join, leave
- `TournamentBracketController` — show (ver bracket/fixture)
- `TournamentStandingsController` — show (tabla de posiciones)

### Organizador
- `TournamentOrganizerController` — create, store, edit, update, cancel
- `TournamentMatchOrganizerController` — updateResult, addEvent
- `TournamentFixtureController` — generate, regenerate

### Venue Admin
- `VenueAdmin\TournamentSettingController` — edit, update (habilitar torneos por cancha)
- `VenueAdmin\TournamentRequestController` — index, approve, reject, counterPropose

### API/AJAX
- `TournamentAvailabilityController` — check available dates for a field

---

## RUTAS

### Públicas (auth)
```
GET    /torneos                              → index (listar torneos disponibles)
GET    /torneos/{tournament}                 → show (detalle del torneo)
GET    /torneos/{tournament}/bracket         → bracket (bracket visual)
GET    /torneos/{tournament}/posiciones      → standings (tabla de posiciones)
GET    /torneos/{tournament}/partido/{match} → matchDetail
```

### Organizador (auth + active.user)
```
GET    /torneos/crear                        → create
POST   /torneos                              → store
GET    /torneos/{tournament}/editar          → edit
PUT    /torneos/{tournament}                 → update
POST   /torneos/{tournament}/cancelar        → cancel
POST   /torneos/{tournament}/generar-fixture → generateFixture
POST   /torneos/{tournament}/partido/{match}/resultado → updateMatchResult
POST   /torneos/{tournament}/partido/{match}/evento    → addMatchEvent
```

### Equipos (auth + active.user)
```
GET    /torneos/{tournament}/inscribir       → createTeam
POST   /torneos/{tournament}/equipos         → storeTeam
POST   /torneos/{tournament}/equipos/{team}/unirse → joinTeam
POST   /torneos/{tournament}/equipos/{team}/salir  → leaveTeam
POST   /torneos/{tournament}/equipos/{team}/invitar → invitePlayer
GET    /torneos/{tournament}/equipos/{team}/checkout → teamCheckout
POST   /torneos/{tournament}/equipos/{team}/pagar   → teamPayment
```

### Venue Admin (va/)
```
GET    /va/tournament-settings               → tournamentSettings (config por cancha)
POST   /va/tournament-settings/{field}       → updateTournamentSettings
GET    /va/tournament-requests               → tournamentRequests (solicitudes pendientes)
POST   /va/tournament-requests/{request}/approve    → approve
POST   /va/tournament-requests/{request}/reject     → reject
POST   /va/tournament-requests/{request}/counter    → counterPropose
```

---

## SERVICIOS

### TournamentService
- `createTournament(User, data)` → crea torneo en estado DRAFT
- `submitToVenue(Tournament)` → envía solicitud al complejo, cambia a PENDING_VENUE
- `openRegistration(Tournament)` → cambia a OPEN_REGISTRATION
- `closeRegistration(Tournament)` → cambia a REGISTRATION_CLOSED
- `cancelTournament(Tournament, reason)` → cancela y reembolsa si aplica

### TournamentFixtureService
- `generateSingleElimination(Tournament)` → genera bracket de eliminación directa
- `generateRoundRobin(Tournament)` → genera todas las fechas de liga
- `generateGroupsElimination(Tournament)` → genera grupos + bracket
- `assignDates(Tournament, dates[])` → asigna fechas/horarios a cada partido
- `createReservations(Tournament)` → crea las reservas reales en las canchas

### TournamentStandingsService
- `updateStandings(TournamentMatch)` → actualiza tabla después de un resultado
- `calculatePositions(Tournament, groupNumber?)` → ordena por puntos, diferencia, etc.
- `determineAdvancing(Tournament)` → determina qué equipos pasan de grupos a eliminación

### TournamentPaymentService
- `calculateTeamPrice(Tournament)` → precio de inscripción
- `createPaymentPreference(TournamentTeam)` → crea preference de MercadoPago
- `handlePaymentWebhook(paymentId)` → procesa pago, confirma equipo
- `calculateSplit(Tournament)` → cuánto va al complejo, cuánto al organizador

---

## NOTIFICACIONES

1. `TournamentVenueRequestNotification` → al dueño del complejo cuando recibe solicitud
2. `TournamentApprovedNotification` → al organizador cuando el complejo aprueba
3. `TournamentRejectedNotification` → al organizador cuando el complejo rechaza
4. `TournamentPublishedNotification` → a usuarios del deporte cuando se abre inscripción
5. `TournamentTeamConfirmedNotification` → al capitán cuando se confirma el pago
6. `TournamentMatchReminderNotification` → a los equipos X horas antes del partido
7. `TournamentMatchResultNotification` → a ambos equipos después de cargar resultado
8. `TournamentAdvancedNotification` → al equipo que avanza de ronda/grupo
9. `TournamentEliminatedNotification` → al equipo eliminado
10. `TournamentFinishedNotification` → a todos los participantes con resumen final

---

## VISTAS

### Públicas
- `torneos/index.blade.php` — listado con filtros (deporte, estado, zona)
- `torneos/show.blade.php` — detalle: info, equipos inscriptos, fixture, posiciones
- `torneos/bracket.blade.php` — bracket visual (eliminación) o tabla (liga)
- `torneos/match.blade.php` — detalle de un partido específico

### Organizador
- `torneos/create.blade.php` — formulario de creación (wizard multi-step)
- `torneos/edit.blade.php` — editar torneo
- `torneos/manage.blade.php` — panel del organizador (cargar resultados, ver pagos)

### Equipos
- `torneos/teams/create.blade.php` — inscribir equipo
- `torneos/teams/show.blade.php` — detalle del equipo
- `torneos/teams/checkout.blade.php` — pago de inscripción

### Venue Admin
- `va/tournament-settings.blade.php` — habilitar/configurar torneos por cancha
- `va/tournament-requests.blade.php` — ver y gestionar solicitudes

---

## EVENTOS BROADCAST (real-time)

- `TournamentTeamJoined` — cuando un equipo se inscribe
- `TournamentMatchStarted` — cuando arranca un partido
- `TournamentMatchFinished` — cuando se carga un resultado
- `TournamentBracketUpdated` — cuando se actualiza el bracket

---

## FASES DE IMPLEMENTACIÓN

### Fase 1: Base (MVP)
- Modelos y migraciones
- TournamentSetting (habilitar por cancha)
- Crear torneo (solo eliminación directa)
- Solicitud al complejo + aprobación
- Inscripción de equipos (sin pago, solo registro)
- Fixture automático de eliminación directa
- Cargar resultados
- Bracket visual básico

### Fase 2: Pagos
- Pago de inscripción via MercadoPago
- Split de pago (complejo + organizador)
- Webhook handling para torneos

### Fase 3: Formatos avanzados
- Liga (round robin)
- Grupos + eliminación
- Tabla de posiciones
- Stats (goleadores, tarjetas)

### Fase 4: Social
- Compartir torneo por WhatsApp
- Chat del torneo
- Notificaciones push
- Página pública compartible
- Invitar jugadores a equipo

### Fase 5: Premium
- Premios/trofeos virtuales
- MVP por partido/torneo
- Integración con ratings de Falta Uno
- Historial de torneos por usuario/equipo
- Rankings de equipos

---

## CONSIDERACIONES TÉCNICAS

### Base de datos
- SQLite en desarrollo/producción actual — funciona bien para esta escala
- Si migran a MySQL: cambiar strftime() por HOUR(), DATE_FORMAT()

### Pagos
- MercadoPago marketplace split: requiere que el complejo tenga mp_access_token
- Si el complejo no tiene MP conectado, el torneo no puede aprobarse
- Webhook endpoint: /webhooks/mercadopago (ya existe, agregar handler para torneos)

### Performance
- Indexes en: [tournament_id, status], [tournament_id, round], [team_id, status]
- Eager loading: Tournament::with(['teams.players', 'matches.homeTeam', 'matches.awayTeam'])

### Seguridad
- Solo el organizador puede editar/cancelar su torneo
- Solo el dueño del complejo puede aprobar/rechazar
- Solo el capitán puede gestionar su equipo
- Rate limiting en inscripción y pago
- Validar que el organizador no se inscriba en su propio torneo como equipo (o sí, configurable)
