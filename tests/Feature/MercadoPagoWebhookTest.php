<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\ReservationBatch;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura del MercadoPago Webhook — el camino más crítico de pago.
 *  - Verificación de firma HMAC (cuando hay secret configurado)
 *  - Idempotency (mismo payment_id varias veces no duplica acciones)
 *  - Status mapping (approved → PAID, rejected → estado intacto)
 *  - Reserva única + batch
 *  - No procesa reservas EXPIRED/CANCELLED
 *  - Discrepancia de monto (sólo loggea warning, no rechaza)
 */
class MercadoPagoWebhookTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        // Sin secret configurado → tests no validan firma (entorno testing/local)
        config(['services.mercadopago.webhook_secret' => null]);
        config(['services.mercadopago.access_token'   => 'test-token']);
    }

    private function fakeMpPaymentResponse(string $externalReference, string $status = 'approved', float $amount = 5000, int $paymentId = 12345): void
    {
        Http::fake([
            'api.mercadopago.com/v1/payments/*' => Http::response([
                'id'                 => $paymentId,
                'status'             => $status,
                'external_reference' => $externalReference,
                'transaction_amount' => $amount,
            ], 200),
        ]);
    }

    private function postWebhook(int $paymentId = 12345, string $type = 'payment'): \Illuminate\Testing\TestResponse
    {
        return $this->postJson(route('webhooks.mercadopago'), [
            'type'  => $type,
            'data'  => ['id' => (string) $paymentId],
        ]);
    }

    // ─── RESERVA ÚNICA ─────────────────────────────────────────────────────

    public function test_webhook_marks_reservation_as_paid_when_approved(): void
    {
        $field = $this->makeField(price: 5000);
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
            'total_amount' => 5000,
        ]);

        $this->fakeMpPaymentResponse((string) $reservation->id, 'approved', 5000, 999);

        $this->postWebhook(999)->assertOk();

        $reservation->refresh();
        $this->assertSame('PAID', $reservation->status);
        $this->assertSame('999', $reservation->payment_external_id);
        $this->assertSame('mercadopago', $reservation->payment_provider);
        $this->assertNull($reservation->expires_at);
    }

    public function test_webhook_is_idempotent_when_called_twice_with_same_payment(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
            'total_amount' => 5000,
        ]);

        $this->fakeMpPaymentResponse((string) $reservation->id, 'approved', 5000, 777);

        $this->postWebhook(777)->assertOk();
        $this->postWebhook(777)->assertOk();
        $this->postWebhook(777)->assertOk();

        // Sigue siendo PAID con el mismo payment_external_id
        $reservation->refresh();
        $this->assertSame('PAID', $reservation->status);
        $this->assertSame('777', $reservation->payment_external_id);

        // El email se envió una sola vez (no en cada llamada)
        Mail::assertSent(\App\Mail\ReservationPaidMail::class, 1);
    }

    public function test_webhook_does_not_process_payment_for_cancelled_reservation(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->cancelled()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $this->fakeMpPaymentResponse((string) $reservation->id, 'approved', 5000, 555);

        $this->postWebhook(555)->assertOk();

        // No debe cambiar el status
        $this->assertSame('CANCELLED', $reservation->fresh()->status);
        Mail::assertNothingSent();
    }

    public function test_webhook_does_not_process_payment_for_expired_reservation(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->expired()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $this->fakeMpPaymentResponse((string) $reservation->id, 'approved', 5000, 444);

        $this->postWebhook(444)->assertOk();

        $this->assertSame('EXPIRED', $reservation->fresh()->status);
    }

    public function test_webhook_keeps_reservation_pending_when_payment_rejected(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $this->fakeMpPaymentResponse((string) $reservation->id, 'rejected', 5000, 333);

        $this->postWebhook(333)->assertOk();

        $r = $reservation->fresh();
        $this->assertSame('PENDING_PAYMENT', $r->status, 'rejected no debe pisar el status');
        $this->assertSame('rejected', $r->payment_status);
    }

    public function test_webhook_ignores_payment_with_unknown_reservation_id(): void
    {
        $this->fakeMpPaymentResponse('99999', 'approved', 5000, 222);
        // Endpoint devuelve ok=true igual (MP no debe re-intentar)
        $this->postWebhook(222)->assertOk();
    }

    public function test_webhook_handles_missing_external_reference_gracefully(): void
    {
        Http::fake([
            'api.mercadopago.com/v1/payments/*' => Http::response([
                'id'                 => 111,
                'status'             => 'approved',
                'external_reference' => null,
                'transaction_amount' => 5000,
            ], 200),
        ]);
        $this->postWebhook(111)->assertOk();
    }

    public function test_webhook_skips_when_mp_api_call_fails(): void
    {
        Http::fake([
            'api.mercadopago.com/v1/payments/*' => Http::response('error', 500),
        ]);
        $this->postWebhook(123)->assertStatus(500);
    }

    public function test_webhook_ignores_merchant_order_topic(): void
    {
        $this->postJson(route('webhooks.mercadopago'), [
            'topic' => 'merchant_order',
            'id'    => 1,
        ])->assertOk()->assertJson(['ok' => true]);
    }

    public function test_webhook_ignores_non_payment_type(): void
    {
        $this->postJson(route('webhooks.mercadopago'), [
            'type' => 'plan',
            'data' => ['id' => 'x'],
        ])->assertOk();
    }

    // ─── BATCH ─────────────────────────────────────────────────────────────

    public function test_webhook_marks_batch_and_all_reservations_as_paid_when_approved(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();

        $batch = ReservationBatch::create([
            'user_id'      => $user->id,
            'field_id'     => $field->id,
            'subtotal'     => 15000,
            'total_amount' => 15000,
            'currency'     => 'ARS',
            'status'       => 'PENDING_PAYMENT',
            'expires_at'   => now()->addMinutes(15),
        ]);

        $tomorrow = Carbon::tomorrow();
        for ($i = 0; $i < 3; $i++) {
            Reservation::factory()->pendingPayment()->create([
                'field_id'     => $field->id,
                'user_id'      => $user->id,
                'batch_id'     => $batch->id,
                'start_at'     => $tomorrow->copy()->setTime(10 + $i, 0),
                'end_at'       => $tomorrow->copy()->setTime(11 + $i, 0),
                'total_amount' => 5000,
            ]);
        }

        $this->fakeMpPaymentResponse('batch:' . $batch->id, 'approved', 15000, 8888);

        $this->postWebhook(8888)->assertOk();

        $batch->refresh();
        $this->assertSame('PAID', $batch->status);
        $this->assertSame('8888', $batch->payment_external_id);
        $this->assertSame(3, $batch->reservations()->where('status', 'PAID')->count());
    }

    public function test_webhook_batch_is_idempotent(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $batch = ReservationBatch::create([
            'user_id'      => $user->id,
            'field_id'     => $field->id,
            'subtotal'     => 5000,
            'total_amount' => 5000,
            'currency'     => 'ARS',
            'status'       => 'PENDING_PAYMENT',
            'expires_at'   => now()->addMinutes(15),
        ]);
        Reservation::factory()->pendingPayment()->create([
            'field_id'     => $field->id,
            'user_id'      => $user->id,
            'batch_id'     => $batch->id,
            'total_amount' => 5000,
        ]);

        $this->fakeMpPaymentResponse('batch:' . $batch->id, 'approved', 5000, 6666);

        $this->postWebhook(6666);
        $this->postWebhook(6666);
        $this->postWebhook(6666);

        $batch->refresh();
        $this->assertSame('PAID', $batch->status);
        Mail::assertSent(\App\Mail\BatchReservationPaidMail::class, 1);
    }

    // ─── FIRMA HMAC ────────────────────────────────────────────────────────

    public function test_webhook_rejects_request_when_secret_configured_but_signature_missing(): void
    {
        config(['services.mercadopago.webhook_secret' => 'secret-test']);

        $this->postJson(route('webhooks.mercadopago'), [
            'type' => 'payment',
            'data' => ['id' => '123'],
        ])->assertStatus(401);
    }

    public function test_webhook_accepts_request_with_valid_signature(): void
    {
        $secret = 'secret-test';
        config(['services.mercadopago.webhook_secret' => $secret]);

        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
            'total_amount' => 5000,
        ]);

        $this->fakeMpPaymentResponse((string) $reservation->id, 'approved', 5000, 1234);

        $ts        = (string) time();
        $requestId = 'req-test-1';
        $dataId    = '1234';
        $manifest  = "id:{$dataId};request-id:{$requestId};ts:{$ts};";
        $v1        = hash_hmac('sha256', $manifest, $secret);

        $this->withHeaders([
            'x-signature'  => "ts={$ts},v1={$v1}",
            'x-request-id' => $requestId,
        ])->postJson(route('webhooks.mercadopago'), [
            'type' => 'payment',
            'data' => ['id' => $dataId],
        ])->assertOk();

        $this->assertSame('PAID', $reservation->fresh()->status);
    }

    public function test_webhook_rejects_request_with_invalid_signature(): void
    {
        config(['services.mercadopago.webhook_secret' => 'real-secret']);

        $ts = (string) time();
        $this->withHeaders([
            'x-signature'  => "ts={$ts},v1=BADHASH",
            'x-request-id' => 'req-1',
        ])->postJson(route('webhooks.mercadopago'), [
            'type' => 'payment',
            'data' => ['id' => '999'],
        ])->assertStatus(401);
    }
}
