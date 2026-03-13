<!-- resources\views\va\dashboard.blade.php -->

@php
  use App\Support\ReservationStatus;
@endphp

@extends('layouts.admin')

@section('title', 'Panel admin cancha')

@section('page_title', 'Dashboard')
@section('page_subtitle', 'Resumen general de tus complejos y reservas')

  @section('content')
    <h1>Panel admin cancha</h1>

    @if(auth()->user()->role === 'venue_admin' && $membershipAlert)
      <div
        style="
          margin:16px 0 20px 0;
          padding:16px 18px;
          border-radius:16px;
          border:1px solid {{ $membershipAlert['tone'] === 'danger' ? '#f1b9c0' : '#f5d48a' }};
          background: {{ $membershipAlert['tone'] === 'danger' ? '#f8d7da' : '#fff4db' }};
          color: {{ $membershipAlert['tone'] === 'danger' ? '#842029' : '#9a6700' }};
        "
      >
        <div style="font-weight:800; margin-bottom:6px;">
          {{ $membershipAlert['tone'] === 'danger' ? 'Tu membresía vence hoy o muy pronto' : 'Tu membresía está por vencer' }}
        </div>

        <div style="line-height:1.6;">
          Tu acceso como socio vence el
          <strong>{{ $membershipAlert['expires_at']->format('d/m/Y H:i') }}</strong>
          @if($membershipAlert['days_remaining'] > 1)
            (faltan {{ $membershipAlert['days_remaining'] }} días).
          @elseif($membershipAlert['days_remaining'] === 1)
            (falta 1 día).
          @else
            (vence hoy).
          @endif
        </div>

        <div style="margin-top:10px;">
          <a href="{{ route('membership.become') }}" class="btn btn-primary">
            Ver membresía / renovar
          </a>
        </div>
      </div>
    @endif

  <div style="display:flex; gap:10px; flex-wrap:wrap; margin:16px 0 20px 0;">
    <button type="button" onclick="showTab('resumen')" class="tab-btn" data-tab="resumen">Resumen</button>
    <button type="button" onclick="showTab('complejos')" class="tab-btn" data-tab="complejos">Complejos y canchas</button>
    <button type="button" onclick="showTab('reservas')" class="tab-btn" data-tab="reservas">Próximas reservas</button>
    <button type="button" onclick="showTab('destacados')" class="tab-btn" data-tab="destacados">Destacados</button>

    @if(auth()->user()->role === 'super_admin')
      <button type="button" onclick="showTab('global')" class="tab-btn" data-tab="global">Vista global</button>
    @endif
  </div>

  <style>
    .tab-btn {
      padding: 10px 16px;
      border: 1px solid #ddd;
      background: #fff;
      border-radius: 999px;
      cursor: pointer;
      font-weight: 600;
      transition: .2s ease;
    }

    .tab-btn:hover {
      background: #f7f7f7;
    }

    .tab-btn.active {
      background: #111;
      color: #fff;
      border-color: #111;
    }

    .tab-pane {
      display: none;
    }

    .tab-pane.active {
      display: block;
    }

    .card-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 14px;
      margin: 18px 0;
    }

    .metric-card {
      padding: 16px;
      border: 1px solid #eee;
      border-radius: 16px;
      background: #fff;
      box-shadow: 0 2px 10px rgba(0,0,0,.03);
    }

    .metric-label {
      font-size: 12px;
      color: #666;
      margin-bottom: 6px;
    }

    .metric-value {
      font-size: 28px;
      font-weight: 700;
      color: #111;
    }

    .panel-card {
      padding: 16px;
      border: 1px solid #eee;
      border-radius: 16px;
      background: #fff;
      box-shadow: 0 2px 10px rgba(0,0,0,.03);
      margin-bottom: 16px;
    }

    .soft-table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border: 1px solid #eee;
      border-radius: 16px;
      overflow: hidden;
    }

    .soft-table th {
      text-align: left;
      background: #fafafa;
      color: #555;
      font-size: 13px;
      padding: 12px;
      border-bottom: 1px solid #eee;
    }

    .soft-table td {
      padding: 12px;
      border-bottom: 1px solid #f1f1f1;
      vertical-align: top;
    }

    .soft-table tr:last-child td {
      border-bottom: 0;
    }

    .section-title {
      margin: 0 0 12px 0;
    }

    .muted {
      color: #666;
      font-size: 13px;
    }

    .two-col-grid {
      display:grid;
      grid-template-columns:1.1fr .9fr;
      gap:18px;
      align-items:start;
    }

    @media (max-width: 900px) {
      .two-col-grid {
        grid-template-columns:1fr;
      }
    }
  </style>

  {{-- RESUMEN --}}
  <div id="tab-resumen" class="tab-pane active">
    <div class="card-grid">
      <div class="metric-card">
        <div class="metric-label">Complejos</div>
        <div class="metric-value">{{ $venues->count() }}</div>
      </div>

      <div class="metric-card">
        <div class="metric-label">Reservas hoy</div>
        <div class="metric-value">{{ $stats['reservations_today_count'] }}</div>
      </div>

      <div class="metric-card">
        <div class="metric-label">Ingresos hoy</div>
        <div class="metric-value">$ {{ number_format($revenueToday, 0, ',', '.') }}</div>
      </div>

      <div class="metric-card">
        <div class="metric-label">Reservas esta semana</div>
        <div class="metric-value">{{ $reservationsWeek }}</div>
      </div>

      <div class="metric-card" style="min-width:220px;">
        <div class="metric-label">Ingresos esta semana</div>
        <div class="metric-value">$ {{ number_format($revenueWeek, 0, ',', '.') }}</div>
      </div>
    </div>

    <div style="margin:18px 0;">
      <a href="{{ route('va.venues.create') }}">+ Crear complejo</a>
      — <a href="{{ route('va.reservations.index') }}">Ver reservas</a>
      — <a href="{{ route('va.reservations.agenda') }}">Ver agenda del día</a>
      — <a href="{{ route('va.blocks.index') }}">Bloquear horarios</a>
      — <a href="{{ route('va.discounts.index') }}">Gestionar descuentos</a>
    </div>

    <div class="two-col-grid">
      <div class="panel-card">
        <h2 class="section-title">Próximas reservas de hoy</h2>

        @if($upcomingToday->isEmpty())
          <p class="muted" style="margin:0;">No hay reservas cargadas para hoy.</p>
        @else
          <div style="display:grid; gap:12px;">
            @foreach($upcomingToday as $reservation)
              <div style="padding:14px; border:1px solid #eee; border-radius:12px;">
                <div style="display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                  <div>
                    <div style="font-weight:700;">
                      {{ $reservation->field->venue->name }} — {{ $reservation->field->name }}
                    </div>
                    <div class="muted" style="margin-top:4px;">
                      {{ $reservation->start_at->format('H:i') }} - {{ $reservation->end_at->format('H:i') }}
                    </div>
                    <div class="muted" style="margin-top:4px;">
                      Usuario: {{ $reservation->user->name ?? '—' }}
                    </div>
                  </div>

                  <div>
                    <span style="padding:4px 10px; border-radius:999px; font-weight:600; {{ ReservationStatus::color($reservation->status) }}">
                      {{ ReservationStatus::label($reservation->status) }}
                    </span>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <div style="display:grid; gap:18px;">
        <div class="panel-card">
          <h2 class="section-title">Cancha más reservada</h2>

          @if($topField)
            <div style="font-size:20px; font-weight:700; margin-bottom:6px;">
              {{ $topField->field->venue->name }} — {{ $topField->field->name }}
            </div>

            <div class="muted">
              {{ $topField->reservations_count }} reserva{{ $topField->reservations_count > 1 ? 's' : '' }} esta semana
            </div>
          @else
            <p class="muted" style="margin:0;">Todavía no hay suficientes datos esta semana.</p>
          @endif
        </div>

        <div class="panel-card">
          <h2 class="section-title">Tus complejos</h2>

          @if($venues->isEmpty())
            <p class="muted" style="margin:0;">No hay complejos cargados.</p>
          @else
            <div style="display:grid; gap:10px;">
              @foreach($venues as $venue)
                <div style="padding:12px; border:1px solid #eee; border-radius:12px;">
                  <div style="font-weight:700;">{{ $venue->name }}</div>
                  <div class="muted" style="margin-top:4px;">
                    {{ $venue->fields->count() }} cancha{{ $venue->fields->count() !== 1 ? 's' : '' }}
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- COMPLEJOS --}}
  <div id="tab-complejos" class="tab-pane">
    <h2 class="section-title">Mis complejos</h2>

    @forelse($venues as $v)
      <div class="panel-card">
        <strong>{{ $v->name }}</strong>
        @if($v->description)
          <span class="muted">— {{ $v->description }}</span>
        @endif

    <div style="margin-top:8px; display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
        <a href="{{ route('va.venues.edit', $v) }}">Editar complejo</a>

        @if($v->mp_access_token)
            <span style="color:#2e7d32; font-weight:600; font-size:13px;">✓ Mercado Pago conectado</span>
        @else
            <span style="color:#999; font-size:13px;">Sin Mercado Pago — <a href="{{ route('va.venues.edit', $v) }}">configurar</a></span>
        @endif
    </div>

        @if($v->fields->isEmpty())
          <p class="muted" style="margin-top:10px;">No tiene canchas cargadas.</p>
        @else
          <ul style="padding-left:18px; margin-top:12px;">
            @foreach($v->fields as $f)
              <li style="margin-bottom:8px; display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                <span>{{ $f->name }}</span>
                <span class="muted">— {{ $f->price->price_per_slot ?? 0 }} {{ $f->price->currency ?? 'ARS' }}</span>
                <a href="{{ route('va.fields.edit', $f) }}">Editar</a>
                <a href="{{ route('va.schedule.edit', $f) }}">Horarios</a>

                <form method="POST" action="{{ route('va.fields.toggle_active', $f) }}" style="display:inline;">
                  @csrf
                  @if($f->is_active)
                    <button type="submit"
                      style="background:none; border:none; color:#c62828; cursor:pointer; font-size:13px; padding:0; font-weight:600;">
                      Desactivar
                    </button>
                  @else
                    <span style="color:#999; font-size:13px; font-weight:600;">Inactiva</span>
                    <button type="submit"
                      style="background:none; border:none; color:#2e7d32; cursor:pointer; font-size:13px; padding:0; font-weight:600;">
                      Activar
                    </button>
                  @endif
                </form>
              </li>
            @endforeach
          </ul>
        @endif

        <a href="{{ route('va.fields.create', $v) }}">+ Agregar cancha</a>
      </div>
    @empty
      <p>No tenés complejos todavía.</p>
    @endforelse
  </div>

  {{-- RESERVAS --}}
  <div id="tab-reservas" class="tab-pane">
    <h2 class="section-title">Próximas reservas de hoy</h2>

    @if($upcomingToday->isEmpty())
      <p>No hay reservas para hoy.</p>
    @else
      <table class="soft-table">
        <tr>
          <th>Hora</th>
          <th>Complejo</th>
          <th>Cancha</th>
          <th>Usuario</th>
          <th>Estado</th>
          <th>Monto</th>
        </tr>

        @foreach($upcomingToday as $r)
          <tr>
            <td>{{ $r->start_at->format('H:i') }} - {{ $r->end_at->format('H:i') }}</td>
            <td>{{ $r->field->venue->name }}</td>
            <td>{{ $r->field->name }}</td>
            <td>{{ $r->user->name ?? '—' }}</td>

            <td>
              <span style="padding:4px 10px; border-radius:999px; font-weight:600; {{ ReservationStatus::color($r->status) }}">
                {{ ReservationStatus::label($r->status) }}
              </span>
            </td>

            <td>{{ $r->currency }} {{ number_format($r->total_amount, 0, ',', '.') }}</td>
          </tr>
        @endforeach
      </table>
    @endif
  </div>

  {{-- DESTACADOS --}}
  <div id="tab-destacados" class="tab-pane">
    <div class="two-col-grid">
      <div class="panel-card">
        <h2 class="section-title">Cancha más reservada</h2>

        @if($topField)
          <div style="font-size:22px; font-weight:700; margin-bottom:8px;">
            {{ $topField->field->name }}
          </div>

          <div class="muted" style="margin-bottom:8px;">
            Complejo: {{ $topField->field->venue->name }}
          </div>

          <div style="font-weight:700;">
            {{ $topField->reservations_count }} reserva{{ $topField->reservations_count > 1 ? 's' : '' }} esta semana
          </div>
        @else
          <p class="muted" style="margin:0;">Todavía no hay suficientes datos esta semana.</p>
        @endif
      </div>

      <div class="panel-card">
        <h2 class="section-title">Resumen rápido</h2>

        <div style="display:grid; gap:10px;">
          <div style="padding:12px; border:1px solid #eee; border-radius:12px;">
            <strong>Reservas hoy:</strong> {{ $stats['reservations_today_count'] }}
          </div>

          <div style="padding:12px; border:1px solid #eee; border-radius:12px;">
            <strong>Ingresos hoy:</strong> $ {{ number_format($revenueToday, 0, ',', '.') }}
          </div>

          <div style="padding:12px; border:1px solid #eee; border-radius:12px;">
            <strong>Reservas esta semana:</strong> {{ $reservationsWeek }}
          </div>

          <div style="padding:12px; border:1px solid #eee; border-radius:12px;">
            <strong>Ingresos esta semana:</strong> $ {{ number_format($revenueWeek, 0, ',', '.') }}
          </div>
        </div>
      </div>
    </div>
  </div>

  @if(auth()->user()->role === 'super_admin')
    <div id="tab-global" class="tab-pane">
      <div class="card-grid">
        <div class="metric-card">
          <div class="metric-label">Usuarios totales</div>
          <div class="metric-value">{{ $superStats['users_count'] }}</div>
        </div>

        <div class="metric-card">
          <div class="metric-label">Admins de complejo</div>
          <div class="metric-value">{{ $superStats['venue_admins_count'] }}</div>
        </div>

        <div class="metric-card">
          <div class="metric-label">Superadmins</div>
          <div class="metric-value">{{ $superStats['super_admins_count'] }}</div>
        </div>

        <div class="metric-card">
          <div class="metric-label">Complejos</div>
          <div class="metric-value">{{ $superStats['venues_count'] }}</div>
        </div>

        <div class="metric-card">
          <div class="metric-label">Canchas</div>
          <div class="metric-value">{{ $superStats['fields_count'] }}</div>
        </div>

        <div class="metric-card">
          <div class="metric-label">Reservas hoy</div>
          <div class="metric-value">{{ $superStats['reservations_today_count'] }}</div>
        </div>

        <div class="metric-card">
          <div class="metric-label">Ingresos hoy</div>
          <div class="metric-value">$ {{ number_format($superStats['revenue_today'], 0, ',', '.') }}</div>
        </div>

        <div class="metric-card">
          <div class="metric-label">Reservas semana</div>
          <div class="metric-value">{{ $superStats['reservations_week_count'] }}</div>
        </div>

        <div class="metric-card">
          <div class="metric-label">Ingresos semana</div>
          <div class="metric-value">$ {{ number_format($superStats['revenue_week'], 0, ',', '.') }}</div>
        </div>
      </div>

      <div class="two-col-grid">
        <div class="panel-card">
          <h2 class="section-title">Complejo más reservado</h2>

          @if($topVenue)
            <div style="font-size:22px; font-weight:700; margin-bottom:8px;">
              {{ $topVenue->venue_name }}
            </div>

            <div class="muted">
              {{ $topVenue->reservations_count }} reserva{{ $topVenue->reservations_count > 1 ? 's' : '' }} esta semana
            </div>
          @else
            <p class="muted" style="margin:0;">Todavía no hay suficientes datos.</p>
          @endif
        </div>

        <div class="panel-card">
          <h2 class="section-title">Usuarios recientes</h2>

          @if($recentUsers->isEmpty())
            <p class="muted" style="margin:0;">No hay usuarios recientes.</p>
          @else
            <div style="display:grid; gap:10px;">
              @foreach($recentUsers as $recentUser)
                <div style="padding:12px; border:1px solid #eee; border-radius:12px;">
                  <div style="font-weight:700;">{{ $recentUser->name }}</div>
                  <div class="muted" style="margin-top:4px;">{{ $recentUser->email }}</div>
                  <div style="margin-top:6px;">
                    <span class="badge">{{ $recentUser->role }}</span>
                  </div>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  @endif

  <script>
    function showTab(tabName) {
      document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
      document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));

      const pane = document.getElementById('tab-' + tabName);
      if (pane) {
        pane.classList.add('active');
      }

      const clicked = [...document.querySelectorAll('.tab-btn')]
        .find(btn => btn.dataset.tab === tabName);

      if (clicked) {
        clicked.classList.add('active');
      }

      localStorage.setItem('va_dashboard_active_tab', tabName);
    }

    document.addEventListener('DOMContentLoaded', () => {
      const savedTab = localStorage.getItem('va_dashboard_active_tab') || 'resumen';
      showTab(savedTab);
    });
  </script>
@endsection