<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\RecurringSubscription;
use App\Services\RecurringReservationSubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecurringSubscriptionController extends Controller
{
    public function __construct(
        private readonly RecurringReservationSubscriptionService $service,
    ) {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'field_id'    => ['required', 'integer', 'exists:fields,id'],
            'start_at'    => ['required', 'date'],
            'frequency'   => ['required', 'in:weekly,biweekly'],
            'occurrences' => ['required', 'integer', 'min:2', 'max:12'],
        ]);

        $field = Field::query()
            ->with(['venue', 'price', 'schedules', 'exceptions'])
            ->findOrFail($data['field_id']);

        if (($field->venue->recurring_payment_mode ?? null) !== 'subscription') {
            abort(422, 'Este complejo no tiene habilitado el pago por suscripción.');
        }

        $start      = Carbon::parse($data['start_at'])->seconds(0);
        $dayOfWeek  = $start->dayOfWeek;
        $startTime  = $start->format('H:i:s');

        $existing = RecurringSubscription::where('user_id', $request->user()->id)
            ->where('field_id', $field->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', $startTime)
            ->where('status', 'ACTIVE')
            ->first();

        if ($existing) {
            return back()->with('error', 'Ya tenés una suscripción activa para este horario.');
        }

        $monthlyAmount = $this->service->calculateMonthlyAmount($field, $data['frequency']);

        $subscription = RecurringSubscription::create([
            'user_id'        => $request->user()->id,
            'field_id'       => $field->id,
            'status'         => 'PENDING_PAYMENT',
            'frequency'      => $data['frequency'],
            'occurrences'    => (int) $data['occurrences'],
            'day_of_week'    => $dayOfWeek,
            'start_time'     => $startTime,
            'slot_minutes'   => $field->slot_minutes,
            'monthly_amount' => $monthlyAmount,
            'currency'       => $field->price?->currency ?? 'ARS',
        ]);

        [$success, $responseData] = $this->service->createPreapproval($subscription);

        if (!$success) {
            $subscription->delete();

            $mpMessage = $responseData['message'] ?? '';
            $error = match (true) {
                str_contains($mpMessage, 'lower than') => 'El monto es menor al mínimo permitido por MercadoPago ($15). Probá con más turnos.',
                default                                => 'No se pudo iniciar el pago. Intentá de nuevo.',
            };

            return back()->with('error', $error);
        }

        if (isset($responseData['init_point'])) {
            return redirect()->away($responseData['init_point']);
        }

        $subscription->delete();
        return back()->with('error', 'Respuesta inesperada de Mercado Pago.');
    }

    public function result(Request $request, RecurringSubscription $subscription)
    {
        if ($subscription->user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        $subscription->load(['field.venue']);

        // Intentar sincronizar estado con MP al cargar la página (no esperar al polling)
        if ($subscription->status === 'PENDING_PAYMENT' && $subscription->mp_preapproval_id) {
            $this->service->syncPreapprovalStatus($subscription);
            $subscription->refresh();
            $subscription->load(['field.venue']);
        }

        $nextOccurrences = $subscription->nextOccurrences(4);

        return view('recurring-subscriptions.result', compact('subscription', 'nextOccurrences'));
    }

    public function statusCheck(Request $request, RecurringSubscription $subscription)
    {
        if ($subscription->user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        // Si sigue pendiente y tiene preapproval_id, consultar a MP directamente
        if ($subscription->status === 'PENDING_PAYMENT' && $subscription->mp_preapproval_id) {
            $this->service->syncPreapprovalStatus($subscription);
            $subscription->refresh();
        }

        return response()->json(['status' => $subscription->status]);
    }

    public function cancel(Request $request, RecurringSubscription $subscription)
    {
        if ($subscription->user_id !== $request->user()->id && $request->user()->role !== 'super_admin') {
            abort(403);
        }

        if (!in_array($subscription->status, ['ACTIVE', 'PENDING_PAYMENT'])) {
            return back()->with('error', 'Esta suscripción no puede cancelarse.');
        }

        $this->service->cancelSubscription($subscription, 'user_requested');

        return redirect()->route('my_reservations')->with('success', 'Suscripción cancelada correctamente.');
    }
}
