# PLAN TECNICO: Sistema de Torneos — TuCancha
## Version 2.0 — Modelo freemium con suscripcion

---

## MODELO DE NEGOCIO

### Por que suscripcion y no comision

La comision no funciona porque:
1. El organizador y el complejo pueden arreglar el pago por fuera (efectivo, transferencia directa)
2. No hay forma tecnica de forzar que el pago pase por la plataforma
3. En Argentina, la cultura es "transferime directo y te hago precio"

La suscripcion desacopla el ingreso de TuCancha del flujo de dinero entre organizador y complejo. TuCancha cobra por el acceso a la herramienta, no por intermediar pagos.

### Tiers

| | Gratis | Pro |
|---|---|---|
| **Precio** | $0 | $X/mes (suscripcion recurrente via MP) |
| **Torneos activos** | 1 a la vez | Ilimitados |
| **Equipos por torneo** | Hasta 8 | Sin limite |
| **Formatos** | Solo eliminacion directa | Eliminacion + Liga + Grupos + Eliminacion |
| **Bracket/fixture** | Basico | Completo con estadisticas |
| **Resultados** | Carga manual | Carga manual + notificaciones automaticas a equipos |
| **Estadisticas** | No | Goleadores, tarjetas, MVP, historial |
| **Cobro de inscripcion** | Se arregla por fuera (efectivo, transferencia) | MercadoPago integrado (cobro automatico a equipos) |
| **Marca** | "Organizado en TuCancha" en todo (header, footer, link compartible) | Logo propio del torneo, sin marca TuCancha |
| **Pagina publica** | Si, con branding TuCancha | Si, personalizable |
| **Compartir por WhatsApp** | Link con preview y marca TuCancha | Link con preview y marca propia |

### Que gana TuCancha con los gratuitos

1. **Trafico y SEO**: cada torneo es una pagina indexable con contenido unico
2. **Usuarios nuevos**: los jugadores que se inscriben crean cuenta → potenciales clientes de reservas y Falta Uno
3. **Conversion a Pro**: organizador que hace 1 torneo gratis y le va bien → quiere liga, stats, mas equipos → paga Pro
4. **Datos**: deportes, zonas y formatos que se mueven mas

### Flujo de dinero

TuCancha NO intermedia pagos de inscripcion en el tier gratis. En el tier Pro, ofrece cobro integrado como feature de valor (no como mecanismo de comision).

```
GRATIS:
  Organizador ←→ Equipos: arreglan por fuera (efectivo, transferencia, lo que quieran)
  Organizador ←→ Complejo: arreglan por fuera
  TuCancha: no toca plata, solo provee la herramienta

PRO:
  Organizador paga suscripcion mensual a TuCancha (via MP Preapproval)
  Equipos pagan inscripcion via MP (va a cuenta del organizador, si tiene OAuth conectado)
  Organizador ←→ Complejo: arreglan por fuera (o usan el sistema automatico si el complejo tiene MP)
  TuCancha: cobra la suscripcion, no cobra comision sobre inscripciones
```

---

## CONCEPTO GENERAL

Cualquier usuario puede crear un torneo. El torneo puede o no estar vinculado a un complejo de la plataforma. Hay dos caminos para coordinar con el complejo:

### Camino 1: Manual (MVP)
- El organizador contacta al complejo por fuera (WhatsApp, telefono, mail, Instagram)
- Arreglan precio, horarios y forma de pago directamente
- En TuCancha, el organizador carga los datos del complejo manualmente (nombre, direccion)
- TuCancha se usa solo para: fixture, bracket, resultados, inscripcion de equipos, pagina publica

### Camino 2: Automatico (futuro)
- El complejo tiene torneos habilitados en su panel de TuCancha
- El organizador envia propuesta desde la plataforma
- El complejo aprueba/rechaza
- Las canchas se reservan automaticamente
- El pago al complejo puede ser: por transferencia directa, "todas las canchas del mes", o via MP si ambos tienen OAuth

Ambos caminos comparten los mismos modelos de datos para torneos, equipos, partidos, fixture y resultados.

---

## FLUJO COMPLETO

### Fase 1: Configuracion del complejo (venue admin) — solo camino automatico
1. El dueno del complejo habilita "Acepto torneos" por cancha
2. Configura:
   - Precio especial por partido de torneo (puede ser distinto al precio normal)
   - Horarios disponibles para torneos (ej: solo fines de semana)
   - Metodo de contacto preferido (WhatsApp, telefono, email, chat interno)
   - Si requiere aprobacion manual o acepta automaticamente
   - Notas/condiciones especiales

### Fase 2: Creacion del torneo (usuario organizador)
1. El sistema verifica el tier del organizador:
   - **Gratis**: puede crear si no tiene otro torneo activo, max 8 equipos, solo eliminacion directa
   - **Pro**: sin restricciones
2. El usuario va a /torneos/crear
3. Configura el torneo:
   - Nombre del torneo
   - Deporte
   - Formato: eliminacion directa (gratis) | liga, grupos + eliminacion (Pro)
   - Cantidad de equipos (max 8 gratis, sin limite Pro)
   - Jugadores por equipo
   - Genero (masculino, femenino, mixto)
   - Categoria (recreativo, intermedio, avanzado, competitivo)
   - Precio de inscripcion por equipo (informativo en gratis, cobrable en Pro)
   - Fecha de inicio estimada
   - Reglas/descripcion
   - Cover image (opcional)
4. Elige como coordinar la cancha:
   - **"Ya tengo cancha"** (camino manual): carga nombre, direccion y contacto del complejo
   - **"Buscar complejo en TuCancha"** (camino automatico): selecciona complejo y cancha, envia propuesta

### Fase 3: Aprobacion del complejo (solo camino automatico)
1. El dueno recibe notificacion (push + email)
2. Ve el detalle: organizador, torneo, fechas, cantidad de partidos
3. Puede:
   - Aprobar → se bloquean las canchas/horarios
   - Rechazar (con motivo)
   - Proponer cambios (sugiere otros horarios)
4. Al aprobar, el torneo pasa a estado APPROVED

### Fase 4: Publicacion e inscripcion de equipos
1. El organizador publica el torneo → estado OPEN_REGISTRATION
2. El torneo aparece en /torneos (listado publico) con la pagina publica
3. El organizador comparte el link por WhatsApp (con Open Graph preview)
4. Inscripcion de equipos:
   - Nombre del equipo
   - Logo/escudo (opcional)
   - Capitan (el que inscribe)
   - Jugadores (invitar por email/username o cargar nombres)
5. Pago de inscripcion:
   - **Gratis**: el organizador marca manualmente "Pago recibido" cuando el capitan le paga por fuera
   - **Pro**: pago automatico via MercadoPago (va a la cuenta del organizador)
6. Cuando se completan todos los equipos y pagos → se genera el fixture

### Fase 5: Torneo en curso
1. El fixture se genera automaticamente segun el formato
2. Cada partido del fixture tiene:
   - Equipos enfrentados
   - Fecha y hora
   - Cancha asignada (nombre, no necesariamente vinculada a la BD)
   - Estado: pendiente, en curso, finalizado, suspendido
3. El organizador carga resultados de cada partido
4. Se actualiza automaticamente:
   - Bracket (eliminacion)
   - Tabla de posiciones (liga/grupos) — solo Pro
   - Goleadores / estadisticas — solo Pro
5. Notificaciones a equipos (solo Pro): proximo partido, resultados, cambios

### Fase 6: Finalizacion
1. Se determina el campeon segun el formato
2. Se genera resumen del torneo
3. Stats y MVP (solo Pro)
4. El torneo pasa a estado FINISHED
5. Queda visible como historial (pagina publica permanente)

---

## MODELOS Y MIGRACIONES

### organizer_subscriptions (suscripcion del organizador)
```
id
user_id (FK → users, unique)
plan (enum: 'free', 'pro')
status (enum: 'active', 'cancelled', 'expired', 'trial')
mp_preapproval_id (string, nullable — ID de suscripcion en MercadoPago)
trial_ends_at (datetime, nullable)
current_period_start (datetime, nullable)
current_period_end (datetime, nullable)
cancelled_at (datetime, nullable)
timestamps
```

### tournament_settings (config por cancha — solo camino automatico)
```
id
field_id (FK → fields, unique)
tournament_enabled (boolean, default: false)
tournament_price_per_match (decimal 12,2, nullable — precio especial, si null usa el normal)
available_days (JSON, nullable — ej: [0,6] para sab/dom, null = todos)
available_start_time (time, nullable)
available_end_time (time, nullable)
contact_method (enum: 'whatsapp', 'phone', 'email', 'internal')
contact_value (string, nullable)
auto_approve (boolean, default: false)
notes (text, nullable — condiciones especiales)
timestamps
```

### tournaments
```
id
organizer_user_id (FK → users)
organizer_tier (enum: 'free', 'pro' — tier al momento de crear, para auditar)

-- Cancha: camino automatico (nullable)
field_id (FK → fields, nullable)
venue_id (FK → venues, nullable)

-- Cancha: camino manual (nullable)
external_venue_name (string, nullable — nombre del complejo si no esta en TuCancha)
external_venue_address (string, nullable)
external_venue_contact (string, nullable — telefono, WA, etc)

-- Config del torneo
name (string)
description (text, nullable)
sport (string — football, padel, tennis, basketball, volleyball)
format (enum: 'single_elimination', 'round_robin', 'groups_elimination')
max_teams (int)
players_per_team (int)
gender_filter (enum: 'male', 'female', 'mixed')
category (string, nullable — recreativo, intermedio, avanzado, competitivo)
inscription_price (decimal 12,2, nullable — informativo o cobrable segun tier)
inscription_currency (string, default: 'ARS')
estimated_start_date (date)
actual_start_date (date, nullable)
rules (text, nullable)
cover_image_path (string, nullable)

-- Estado
status (enum: 'draft', 'pending_venue', 'venue_rejected', 'open_registration', 'registration_closed', 'in_progress', 'finished', 'cancelled')
venue_rejection_reason (text, nullable)
venue_approved_at (datetime, nullable)
registration_deadline (datetime, nullable)

-- Formato grupos + eliminacion (solo Pro)
groups_count (int, nullable)
teams_per_group (int, nullable)
advancing_per_group (int, nullable)

-- Cancelacion
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
group_number (int, nullable — en que grupo esta)

-- Pago (solo relevante en tier Pro con MP integrado)
payment_confirmed (boolean, default: false)
payment_confirmed_at (datetime, nullable)
payment_method (enum: 'manual', 'mercadopago', nullable)
payment_external_id (string, nullable)
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

-- Cancha (puede ser del sistema o texto libre)
field_id (FK → fields, nullable)
venue_name_override (string, nullable — si es camino manual, nombre de la cancha)

-- Reserva real (solo camino automatico)
reservation_id (FK → reservations, nullable)

scheduled_at (datetime)
status (enum: 'scheduled', 'in_progress', 'finished', 'suspended', 'cancelled', 'walkover')
home_score (int, nullable)
away_score (int, nullable)
home_penalties (int, nullable — para definicion por penales)
away_penalties (int, nullable)
winner_team_id (FK → tournament_teams, nullable)
notes (text, nullable)
played_at (datetime, nullable)
timestamps

index: [tournament_id, round, match_number]
```

### tournament_standings (tabla de posiciones — solo Pro, liga/grupos)
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

### tournament_match_events (goles, tarjetas — solo Pro)
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

### tournament_venue_requests (solicitudes al complejo — solo camino automatico)
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

**Nota:** No hay tabla de settlements/liquidaciones. TuCancha no intermedia pagos. El unico ingreso de TuCancha es la suscripcion del organizador Pro.

---

## CONTROLADORES

### Publico
- `TournamentController` — index (listar torneos), show (pagina publica), search
- `TournamentBracketController` — show (bracket/fixture visual)
- `TournamentStandingsController` — show (tabla de posiciones, solo Pro)

### Organizador
- `TournamentOrganizerController` — create, store, edit, update, cancel, publish
- `TournamentMatchOrganizerController` — updateResult, addEvent (solo Pro)
- `TournamentFixtureController` — generate, regenerate
- `TournamentTeamManageController` — confirmPayment (marcar pago manual), disqualify

### Equipos
- `TournamentTeamController` — create (inscribir equipo), join, leave
- `TournamentTeamPaymentController` — checkout, pay (solo Pro con MP integrado)

### Suscripcion
- `OrganizerSubscriptionController` — plans, subscribe, cancel, webhook

### Venue Admin (solo camino automatico)
- `VenueAdmin\TournamentSettingController` — edit, update
- `VenueAdmin\TournamentRequestController` — index, approve, reject, counterPropose

---

## RUTAS

### Publicas
```
GET    /torneos                              → index
GET    /torneos/{tournament}                 → show (pagina publica)
GET    /torneos/{tournament}/bracket         → bracket
GET    /torneos/{tournament}/posiciones      → standings (solo si Pro)
```

### Organizador (auth + active.user)
```
GET    /torneos/crear                        → create (verifica tier)
POST   /torneos                              → store
GET    /torneos/{tournament}/editar          → edit
PUT    /torneos/{tournament}                 → update
POST   /torneos/{tournament}/publicar        → publish
POST   /torneos/{tournament}/cancelar        → cancel
POST   /torneos/{tournament}/generar-fixture → generateFixture
POST   /torneos/{tournament}/partido/{match}/resultado → updateMatchResult
POST   /torneos/{tournament}/partido/{match}/evento    → addMatchEvent (solo Pro)
POST   /torneos/{tournament}/equipos/{team}/confirmar-pago → confirmPayment (manual)
POST   /torneos/{tournament}/equipos/{team}/descalificar   → disqualify
```

### Equipos (auth + active.user)
```
GET    /torneos/{tournament}/inscribir       → createTeam
POST   /torneos/{tournament}/equipos         → storeTeam
POST   /torneos/{tournament}/equipos/{team}/unirse → joinTeam
POST   /torneos/{tournament}/equipos/{team}/salir  → leaveTeam
POST   /torneos/{tournament}/equipos/{team}/invitar → invitePlayer
GET    /torneos/{tournament}/equipos/{team}/pagar   → teamCheckout (solo Pro)
POST   /torneos/{tournament}/equipos/{team}/pagar   → teamPayment (solo Pro)
```

### Suscripcion (auth + active.user)
```
GET    /organizador/planes                   → plans
POST   /organizador/suscribir               → subscribe
POST   /organizador/cancelar-suscripcion    → cancel
POST   /webhooks/organizer-subscription     → webhook (MP preapproval)
```

### Venue Admin (va/)
```
GET    /va/tournament-settings               → tournamentSettings
POST   /va/tournament-settings/{field}       → updateTournamentSettings
GET    /va/tournament-requests               → tournamentRequests
POST   /va/tournament-requests/{request}/approve    → approve
POST   /va/tournament-requests/{request}/reject     → reject
POST   /va/tournament-requests/{request}/counter    → counterPropose
```

---

## SERVICIOS

### TournamentService
- `createTournament(User, data)` → verifica tier, crea torneo en estado DRAFT
- `publishTournament(Tournament)` → cambia a OPEN_REGISTRATION, genera pagina publica
- `closeRegistration(Tournament)` → cambia a REGISTRATION_CLOSED
- `cancelTournament(Tournament, reason)` → cancela

### TournamentFixtureService
- `generateSingleElimination(Tournament)` → genera bracket de eliminacion directa
- `generateRoundRobin(Tournament)` → genera todas las fechas de liga (solo Pro)
- `generateGroupsElimination(Tournament)` → genera grupos + bracket (solo Pro)
- `assignDates(Tournament, dates[])` → asigna fechas/horarios a cada partido

### TournamentStandingsService (solo Pro)
- `updateStandings(TournamentMatch)` → actualiza tabla despues de un resultado
- `calculatePositions(Tournament, groupNumber?)` → ordena por puntos, diferencia, etc.
- `determineAdvancing(Tournament)` → determina que equipos pasan de grupos a eliminacion

### TournamentPaymentService (solo Pro)
- `createPaymentPreference(TournamentTeam)` → crea preference de MP para inscripcion
- `handlePaymentWebhook(paymentId)` → procesa pago, confirma equipo

### OrganizerSubscriptionService
- `getOrCreateSubscription(User)` → obtiene o crea registro de suscripcion
- `getTier(User)` → retorna 'free' o 'pro'
- `canCreateTournament(User)` → verifica limites del tier
- `subscribe(User, plan)` → crea preapproval en MP
- `cancel(User)` → cancela suscripcion
- `handleWebhook(preapprovalId)` → actualiza estado de suscripcion

---

## MIDDLEWARE

### CheckOrganizerTier
Middleware que verifica el tier del organizador antes de acciones restringidas:

```php
// Uso en rutas:
Route::post('/torneos/{tournament}/partido/{match}/evento', ...)->middleware('organizer.tier:pro');
```

Logica:
1. Si la ruta requiere 'pro' y el usuario tiene tier 'free' → redirigir a /organizador/planes con mensaje
2. Si la ruta requiere 'free' (o no especifica) → dejar pasar
3. Cache del tier en sesion para no consultar DB en cada request

---

## NOTIFICACIONES

### Siempre (gratis y Pro)
1. `TournamentVenueRequestNotification` → al complejo cuando recibe solicitud (camino automatico)
2. `TournamentApprovedNotification` → al organizador cuando el complejo aprueba
3. `TournamentRejectedNotification` → al organizador cuando el complejo rechaza
4. `TournamentTeamRegisteredNotification` → al organizador cuando un equipo se inscribe
5. `TournamentFinishedNotification` → a todos los participantes con resumen

### Solo Pro
6. `TournamentPublishedNotification` → a usuarios del deporte/zona cuando se abre inscripcion
7. `TournamentMatchReminderNotification` → a los equipos X horas antes del partido
8. `TournamentMatchResultNotification` → a ambos equipos despues de cargar resultado
9. `TournamentAdvancedNotification` → al equipo que avanza de ronda/grupo
10. `TournamentEliminatedNotification` → al equipo eliminado

---

## VISTAS

### Publicas
- `torneos/index.blade.php` — listado con filtros (deporte, estado, zona, formato)
- `torneos/show.blade.php` — pagina publica del torneo: info, equipos, fixture, posiciones
  - Tier gratis: con branding TuCancha (header, footer, "Organizado en TuCancha")
  - Tier Pro: branding personalizable (logo del torneo, colores)
- `torneos/bracket.blade.php` — bracket visual (eliminacion) o tabla (liga)

### Organizador
- `torneos/create.blade.php` — wizard multi-step de creacion
  - Paso 1: Info basica (nombre, deporte, formato, equipos)
  - Paso 2: Cancha ("Ya tengo cancha" vs "Buscar en TuCancha")
  - Paso 3: Inscripcion (precio, deadline, reglas)
  - Paso 4: Preview y publicar
- `torneos/edit.blade.php` — editar torneo
- `torneos/manage.blade.php` — panel del organizador (cargar resultados, gestionar equipos/pagos)

### Equipos
- `torneos/teams/create.blade.php` — inscribir equipo
- `torneos/teams/show.blade.php` — detalle del equipo
- `torneos/teams/checkout.blade.php` — pago de inscripcion (solo Pro)

### Suscripcion
- `organizador/planes.blade.php` — comparativa de planes con CTA de suscripcion

### Venue Admin
- `va/tournament-settings.blade.php` — habilitar/configurar torneos por cancha
- `va/tournament-requests.blade.php` — ver y gestionar solicitudes

---

## PAGINA PUBLICA DEL TORNEO (lo mas importante)

La pagina publica (`/torneos/{tournament}`) es el producto principal. Es lo que el organizador comparte por WhatsApp y lo que reemplaza al "grupo de WA + Excel + Paint".

### Open Graph tags (para preview de WhatsApp)
```html
<meta property="og:title" content="Copa Barrio Norte — Futbol 5">
<meta property="og:description" content="8 equipos | Eliminacion directa | Inscripcion $10.000 | Arranca 15/05">
<meta property="og:image" content="https://tucancha.com/torneos/1/og-image.png">
<meta property="og:url" content="https://tucancha.com/torneos/copa-barrio-norte">
```

### Contenido de la pagina
1. **Header**: nombre del torneo, deporte, formato, estado
2. **Info**: fecha, complejo, precio inscripcion, reglas
3. **Equipos inscriptos**: lista con logos/nombres, slots disponibles
4. **Boton CTA**: "Inscribir equipo" (si hay lugar)
5. **Bracket/Fixture**: visual, interactivo
6. **Tabla de posiciones**: solo Pro, solo liga/grupos
7. **Resultados**: lista de partidos jugados
8. **Estadisticas**: solo Pro (goleadores, tarjetas)

### Branding
- **Gratis**: header con logo TuCancha, footer "Organizado en TuCancha | Crea tu torneo gratis", colores de TuCancha
- **Pro**: header con logo del torneo (o del organizador), sin mencion a TuCancha (excepto un link discreto en el footer)

---

## FASES DE IMPLEMENTACION

### Fase 1: MVP (camino manual + eliminacion directa)
**Objetivo:** Un organizador puede crear un torneo, compartir el link, inscribir equipos, generar fixture y cargar resultados. Sin pagos, sin integracion con complejos.

- Modelo `tournaments` (con campos external_venue_*)
- Modelo `tournament_teams` (sin pago, solo registro)
- Modelo `tournament_team_players`
- Modelo `tournament_matches`
- TournamentService: crear, publicar, cancelar
- TournamentFixtureService: solo eliminacion directa
- TournamentController: index, show (pagina publica)
- TournamentOrganizerController: create, store, edit, update, publish, cancel
- TournamentMatchOrganizerController: updateResult
- TournamentTeamController: create, store
- Vistas: index, show (con bracket), create (wizard), manage
- Open Graph tags para compartir por WhatsApp
- Branding TuCancha en todo (tier gratis)

### Fase 2: Suscripcion Pro
- Modelo `organizer_subscriptions`
- OrganizerSubscriptionService (reutilizar MercadoPagoSubscriptionService)
- Middleware CheckOrganizerTier
- Vista de planes
- Desbloqueo de features Pro: formatos avanzados, estadisticas, notificaciones

### Fase 3: Pagos de inscripcion (Pro)
- TournamentPaymentService
- Checkout de inscripcion via MercadoPago
- Webhook handling para confirmar equipos
- Confirmacion manual de pago (para organizadores gratis que quieran trackear)

### Fase 4: Integracion con complejos (camino automatico)
- Modelo `tournament_settings`
- Modelo `tournament_venue_requests`
- Panel venue admin: habilitar torneos, gestionar solicitudes
- Reserva automatica de canchas
- Notificaciones al complejo

### Fase 5: Formatos avanzados (Pro)
- Liga (round robin): fixture + tabla de posiciones
- Grupos + eliminacion: fase de grupos + bracket
- TournamentStandingsService
- Modelo `tournament_standings`
- Modelo `tournament_match_events` (goles, tarjetas)
- Estadisticas: goleadores, MVP

### Fase 6: Social y growth
- Compartir por WhatsApp mejorado (imagen dinamica del bracket)
- Notificaciones push a usuarios de Falta Uno cuando hay torneo de su deporte/zona
- Chat del torneo (reutilizar infraestructura de Falta Uno chat)
- Invitar jugadores a equipo por link
- Rankings de equipos
- Historial de torneos por usuario

---

## CONSIDERACIONES TECNICAS

### Suscripciones
- Reutilizar `MercadoPagoSubscriptionService` que ya existe para venue admins
- Misma API de Preapproval de MercadoPago
- Webhook endpoint separado: `/webhooks/organizer-subscription`

### Performance
- Indexes en: [tournament_id, status], [tournament_id, round], [team_id, status], [organizer_user_id]
- Eager loading: Tournament::with(['teams.players', 'matches.homeTeam', 'matches.awayTeam'])
- Cache de bracket/standings con invalidacion al cargar resultado

### Seguridad
- Solo el organizador puede editar/cancelar su torneo
- Solo el organizador puede cargar resultados y gestionar equipos
- Solo el dueno del complejo puede aprobar/rechazar solicitudes
- Solo el capitan puede gestionar su equipo
- Rate limiting en inscripcion de equipos
- Verificacion de tier en cada accion restringida via middleware

### SEO
- URLs amigables: `/torneos/copa-barrio-norte` (slug)
- Cada torneo es una pagina indexable
- Structured data (Schema.org SportsEvent)
- Sitemap dinamico con torneos activos

### Slug
- Agregar columna `slug` (string, unique) al modelo `tournaments`
- Generar automaticamente desde el nombre al crear
- Usar slug en las URLs publicas en vez de ID
