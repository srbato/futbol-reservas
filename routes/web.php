<?php

use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FavoriteVenueController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\MercadoPagoWebhookController;
use App\Http\Controllers\MyReservationsController;
use App\Http\Controllers\PaymentDevController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationCancelController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationModifyController;
use App\Http\Controllers\RecurringReservationController;
use App\Http\Controllers\RecurringSubscriptionController;
use App\Http\Controllers\ReservationBatchCheckoutController;
use App\Http\Controllers\ReservationBatchMercadoPagoController;
use App\Http\Controllers\VenueAdmin\FieldRecurringDiscountController as VaFieldRecurringDiscountController;
use App\Http\Controllers\ReservationViewController;
use App\Http\Controllers\SystemMessageDismissController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\VenueReviewController;
use App\Http\Controllers\SuperAdmin\PlatformPayoutController;
use App\Http\Controllers\SuperAdmin\SystemMessageController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Http\Controllers\VenueAdmin\DashboardController;
use App\Http\Controllers\VenueAdmin\FieldBlockController as VaFieldBlockController;
use App\Http\Controllers\VenueAdmin\FieldController as VaFieldController;
use App\Http\Controllers\VenueAdmin\FieldDiscountController as VaFieldDiscountController;
use App\Http\Controllers\VenueAdmin\ReportsController as VaReportsController;
use App\Http\Controllers\VenueAdmin\ReservationsController as VaReservationsController;
use App\Http\Controllers\VenueAdmin\ScheduleController as VaScheduleController;
use App\Http\Controllers\VenueAdmin\ManualReservationController as VaManualReservationController;
use App\Http\Controllers\VenueAdmin\ManualRecurringReservationController as VaManualRecurringReservationController;
use App\Http\Controllers\VenueAdmin\VenueController as VaVenueController;
use App\Http\Controllers\VenueAdmin\VenuePayoutController as VaVenuePayoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VenueAdminMembershipController;
use App\Http\Controllers\MercadoPagoOAuthController;
use App\Http\Controllers\OrganizerMercadoPagoOAuthController;
use App\Http\Controllers\SuperAdmin\MembershipPlanController;
use App\Http\Controllers\MatchHistoryController;
use App\Http\Controllers\ReservationPlayerController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\VenueStaffInvitationController;
use App\Http\Controllers\VenueAdmin\VenueStaffController as VaVenueStaffController;
use App\Http\Controllers\VenueAdmin\FaltaUnoSettingController as VaFaltaUnoSettingController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\SuperAdmin\BlogPostController;
use App\Http\Controllers\SuperAdmin\OrganizerPlanController;
use App\Http\Controllers\VenueAdmin\CheckinController as VaCheckinController;
use App\Http\Controllers\FaltaUnoController;
use App\Http\Controllers\FaltaUnoSportProfileController;
use App\Http\Controllers\FaltaUnoProfilePublicController;
use App\Http\Controllers\FaltaUnoChatController;
use App\Http\Controllers\FaltaUnoStatsController;
use App\Http\Controllers\FaltaUnoRatingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TournamentOrganizerController;
use App\Http\Controllers\TournamentTeamController;
use App\Http\Controllers\TournamentTeamManageController;
use App\Http\Controllers\TournamentPaymentController;
use App\Http\Controllers\OrganizerSubscriptionController;
use App\Models\MembershipPlan;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Feedback público (sin auth requerido)
Route::post('/feedback', [FeedbackController::class, 'store'])->middleware('throttle:5,1')->name('feedback.store');

// Google OAuth
Route::get('/auth/google', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\Auth\GoogleAuthController::class, 'callback']);

Route::get('/como-funciona', function () {
    $plan = \App\Models\MembershipPlan::where('is_active', true)->first();
    $longTermMonths = $plan ? $plan->longTermMonths() : 12;
    $longTermLabel = $longTermMonths === 6 ? 'semestral' : 'anual';
    return view('como-funciona', compact('longTermLabel', 'longTermMonths'));
})->name('como-funciona');

Route::get('/nosotros', function () {
    return view('nosotros');
})->name('nosotros');

Route::get('/por-que-tucancha', fn() => view('por-que-tucancha'))->name('por-que-tucancha');

Route::get('/para-complejos', function () {
    $plans = \App\Models\MembershipPlan::where('is_active', true)
        ->orderBy('sort_order')
        ->get();
    $trialDays = $plans->max('trial_days') ?: 7;
    return view('para-complejos', compact('plans', 'trialDays'));
})->name('para-complejos');

Route::get('/preguntas-frecuentes', function () {
    $trialDays = \App\Models\MembershipPlan::where('is_active', true)
        ->whereNotNull('trial_days')
        ->where('trial_days', '>', 0)
        ->max('trial_days') ?? 7;
    return view('faq', compact('trialDays'));
})->name('faq');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::get('/planes', function () {
    $plans = MembershipPlan::where('is_active', true)->orderBy('sort_order')->get();
    $trialDays = $plans->max('trial_days') ?: 7;
    return view('planes', compact('plans', 'trialDays'));
})->name('planes');

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'super_admin') {
        return redirect()->route('sa.users.index');
    }

    if ($user->role === 'venue_admin') {
        return redirect()->route('va.dashboard');
    }

    return redirect()->route('my_reservations');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Auth / Profile
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user'])->group(function () {
    Route::post('/system-messages/{message}/dismiss', [SystemMessageDismissController::class, 'store'])
        ->name('system-messages.dismiss');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/membership/become-partner', [VenueAdminMembershipController::class, 'show'])
        ->name('membership.become');

    Route::post('/membership/checkout', [VenueAdminMembershipController::class, 'checkout'])
        ->name('membership.checkout');

    Route::get('/membership/success', [VenueAdminMembershipController::class, 'success'])
        ->name('membership.success');

    Route::get('/membership/pending', [VenueAdminMembershipController::class, 'pending'])
        ->name('membership.pending');

    Route::get('/membership/failure', [VenueAdminMembershipController::class, 'failure'])
        ->name('membership.failure');

    Route::post('/membership/cancel-pending', [VenueAdminMembershipController::class, 'cancelPending'])
        ->name('membership.cancel_pending');

    Route::post('/membership/cancel-subscription', [VenueAdminMembershipController::class, 'cancelSubscription'])
        ->middleware('throttle:5,1')
        ->name('membership.cancel_subscription');

    Route::post('/membership/start-trial', [VenueAdminMembershipController::class, 'startTrial'])
        ->name('membership.start_trial');
});

/*
|--------------------------------------------------------------------------
| Public Venues / Fields
|--------------------------------------------------------------------------
*/

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
Route::get('/venues/{venue}', [VenueController::class, 'show'])->name('venues.show');
Route::get('/falta-uno', [FaltaUnoController::class, 'index'])->name('falta-uno.index');
Route::get('/falta-uno/{game}', [FaltaUnoController::class, 'show'])->name('falta-uno.show');

Route::get('/fields/{field}', [FieldController::class, 'show'])->name('fields.show');
Route::get('/fields/{field}/availability', [AvailabilityController::class, 'show'])->name('fields.availability');
Route::get('/fields/{field}/recurring-availability', [AvailabilityController::class, 'recurring'])->name('fields.recurring_availability');

/*
|--------------------------------------------------------------------------
| Reservations
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user'])->group(function () {
    Route::post('/reservations', [ReservationController::class, 'store'])
        ->middleware('throttle:20,1')
        ->name('reservations.store');
    Route::post('/reservations/recurring', [RecurringReservationController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('reservations.recurring');

    Route::get('/batches/{batch}/checkout', [ReservationBatchCheckoutController::class, 'show'])->name('batches.checkout');
    Route::post('/batches/{batch}/mercadopago', [ReservationBatchMercadoPagoController::class, 'checkout'])
        ->middleware('throttle:10,1')
        ->name('batches.mercadopago');

    // Suscripciones recurrentes (modo subscription)
    Route::post('/reservas/suscripcion', [RecurringSubscriptionController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('recurring.subscription.store');
    Route::get('/reservas/suscripcion/{subscription}/resultado', [RecurringSubscriptionController::class, 'result'])
        ->name('recurring.subscription.result');
    Route::get('/reservas/suscripcion/{subscription}/estado', [RecurringSubscriptionController::class, 'statusCheck'])
        ->name('recurring.subscription.status');
    Route::post('/reservas/suscripcion/{subscription}/cancelar', [RecurringSubscriptionController::class, 'cancel'])
        ->name('recurring.subscription.cancel');

    Route::get('/reservations/{reservation}', [ReservationViewController::class, 'show'])
        ->name('reservations.show');

    Route::get('/reservations/{reservation}/checkout', [CheckoutController::class, 'show'])
        ->name('reservations.checkout');

    if (app()->environment('local', 'testing')) {
        Route::post('/reservations/{reservation}/pay-dev', [PaymentDevController::class, 'pay'])
            ->name('reservations.pay_dev');
    }

    Route::post('/reservations/{reservation}/mercadopago', [MercadoPagoController::class, 'checkout'])
        ->name('reservations.mercadopago');

    Route::get('/reservations/{reservation}/modify', [ReservationModifyController::class, 'showSlotPicker'])
        ->name('reservations.modify.show');
    Route::post('/reservations/{reservation}/modify/preview', [ReservationModifyController::class, 'previewChange'])
        ->name('reservations.modify.preview');
    Route::post('/reservations/{reservation}/modify/confirm', [ReservationModifyController::class, 'confirm'])
        ->name('reservations.modify.confirm');
    Route::get('/reservations/{reservation}/modify/payment-success', [ReservationModifyController::class, 'modifySuccess'])
        ->name('reservations.modify.payment.success');
    Route::get('/reservations/{reservation}/modify/payment-pending', [ReservationModifyController::class, 'modifyPending'])
        ->name('reservations.modify.payment.pending');
    Route::get('/reservations/{reservation}/modify/payment-failure', [ReservationModifyController::class, 'modifyFailure'])
        ->name('reservations.modify.payment.failure');

    Route::get('/my-reservations', [MyReservationsController::class, 'index'])
        ->name('my_reservations');

    Route::post('/reservations/{reservation}/cancel', [ReservationCancelController::class, 'cancelByUser'])
        ->name('reservations.cancel');

    Route::get('/match-history', [MatchHistoryController::class, 'index'])->name('match_history');
    Route::post('/reservations/{reservation}/result', [MatchHistoryController::class, 'updateResult'])->name('reservations.update_result');
    Route::post('/reservations/{reservation}/players', [ReservationPlayerController::class, 'store'])->name('reservations.players.store');
    Route::post('/reservations/{reservation}/players/{player}', [ReservationPlayerController::class, 'destroy'])->name('reservations.players.destroy');

    Route::get('/referral', [ReferralController::class, 'index'])->name('referral.index')->middleware('role:super_admin');

    // Staff invitations
    Route::get('/staff/invitations/{token}/accept', [VenueStaffInvitationController::class, 'accept'])->name('staff.invitations.accept');
    Route::get('/staff/invitations/{token}/reject', [VenueStaffInvitationController::class, 'reject'])->name('staff.invitations.reject');

    // Falta Uno — partidos
    Route::get('/fields/{field}/falta-uno/create', [FaltaUnoController::class, 'create'])->name('falta-uno.create');
    Route::post('/fields/{field}/falta-uno', [FaltaUnoController::class, 'store'])->name('falta-uno.store');
    Route::post('/falta-uno/{game}/join', [FaltaUnoController::class, 'join'])->name('falta-uno.join');
    Route::post('/falta-uno/{game}/leave', [FaltaUnoController::class, 'leave'])->name('falta-uno.leave');
    Route::post('/falta-uno/{game}/cancel', [FaltaUnoController::class, 'cancel'])->name('falta-uno.cancel');
    Route::post('/falta-uno/{game}/kick/{user}', [FaltaUnoController::class, 'kick'])
        ->middleware('throttle:10,1')
        ->name('falta-uno.kick');
    Route::post('/falta-uno/{game}/no-shows', [FaltaUnoController::class, 'markNoShows'])
        ->name('falta-uno.no-shows');

    // Perfil deportivo propio
    Route::get('/mi-perfil-deportivo', [FaltaUnoSportProfileController::class, 'index'])->name('sport-profile.index');
    Route::get('/mi-perfil-deportivo/crear', [FaltaUnoSportProfileController::class, 'create'])->name('sport-profile.create');
    Route::post('/mi-perfil-deportivo', [FaltaUnoSportProfileController::class, 'store'])->name('sport-profile.store');
    Route::get('/mi-perfil-deportivo/{sport}/editar', [FaltaUnoSportProfileController::class, 'edit'])->name('sport-profile.edit');
    Route::put('/mi-perfil-deportivo/{sport}', [FaltaUnoSportProfileController::class, 'update'])->name('sport-profile.update');

    // Perfil deportivo público
    Route::get('/jugadores/{user}/perfil', [FaltaUnoProfilePublicController::class, 'show'])->name('sport-profile.public');

    // Chat
    Route::get('/falta-uno/{game}/chat', [FaltaUnoChatController::class, 'index'])->name('falta-uno.chat');
    Route::post('/falta-uno/{game}/chat', [FaltaUnoChatController::class, 'store'])->middleware('throttle:30,1')->name('falta-uno.chat.store');

    // Stats personales
    Route::get('/falta-uno/{game}/stats', [FaltaUnoStatsController::class, 'create'])->name('falta-uno.stats');
    Route::post('/falta-uno/{game}/stats', [FaltaUnoStatsController::class, 'store'])->name('falta-uno.stats.store');

    // Calificaciones
    Route::get('/falta-uno/{game}/calificar', [FaltaUnoRatingController::class, 'create'])->name('falta-uno.rate');
    Route::post('/falta-uno/{game}/calificar', [FaltaUnoRatingController::class, 'store'])->name('falta-uno.rate.store');

    // Notificaciones internas
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark_all_read');

    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'store'])->name('push.subscribe');
    Route::delete('/push-subscriptions', [PushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');

    Route::post('/referral/redeem-reservation/{reservation}', [ReferralController::class, 'redeemReservation'])
        ->middleware(['throttle:5,1', 'role:super_admin'])
        ->name('referral.redeem_reservation');
    Route::post('/referral/redeem-month/{reward}', [ReferralController::class, 'redeemMonth'])
        ->middleware(['throttle:5,1', 'role:super_admin'])
        ->name('referral.redeem_month');
});

/*
|--------------------------------------------------------------------------
| Mercado Pago Webhook + Return Screens
|--------------------------------------------------------------------------
*/

Route::post('/webhooks/mercadopago', [MercadoPagoWebhookController::class, 'handle'])
    ->middleware('throttle:120,1')
    ->name('webhooks.mercadopago');

Route::middleware(['auth'])->group(function () {
    Route::get('/batch-success/{batch}', function (\App\Models\ReservationBatch $batch) {
        abort_if($batch->user_id !== auth()->id() && auth()->user()->role !== 'super_admin', 403);
        $batch->load(['field.venue', 'reservations' => fn($q) => $q->orderBy('start_at')]);

        // Si el batch sigue pendiente, verificamos directamente con MP
        if ($batch->status === 'PENDING_PAYMENT' && $batch->mp_preference_id) {
            $accessToken = $batch->field->venue->mp_access_token
                ?? config('services.mercadopago.access_token');
            try {
                $mpResponse = \Illuminate\Support\Facades\Http::withOptions(['verify' => app()->isProduction()])
                    ->withToken($accessToken)
                    ->get('https://api.mercadopago.com/v1/payments/search', [
                        'external_reference' => 'batch:' . $batch->id,
                        'sort'               => 'date_created',
                        'criteria'           => 'desc',
                        'limit'              => 1,
                    ]);
                if ($mpResponse->successful()) {
                    $payment  = ($mpResponse->json('results') ?? [])[0] ?? null;
                    if (($payment['status'] ?? null) === 'approved') {
                        $paymentId = (string) ($payment['id'] ?? '');
                        // Actualizamos el batch y las reservas individuales con todos los campos de pago
                        \Illuminate\Support\Facades\DB::transaction(function () use ($batch, $paymentId) {
                            $freshBatch = \App\Models\ReservationBatch::lockForUpdate()->find($batch->id);
                            if ($freshBatch && $freshBatch->status !== 'PAID') {
                                $freshBatch->reservations()->update([
                                    'status'                 => 'PAID',
                                    'expires_at'             => null,
                                    'payment_provider'       => 'mercadopago',
                                    'payment_external_id'    => $paymentId,
                                    'payment_status'         => 'approved',
                                    'payment_mp_token_owner' => $freshBatch->payment_mp_token_owner,
                                ]);
                                $freshBatch->update([
                                    'status'              => 'PAID',
                                    'payment_status'      => 'approved',
                                    'payment_external_id' => $paymentId,
                                ]);
                            }
                        });
                        $batch->refresh()->load(['field.venue', 'reservations' => fn($q) => $q->orderBy('start_at'), 'user', 'field.venue.owner']);
                        // Enviar emails de confirmación (solo si el batch fue marcado PAID en esta request)
                        if ($batch->payment_external_id === $paymentId && $batch->status === 'PAID') {
                            \Illuminate\Support\Facades\Mail::to($batch->user->email)
                                ->send(new \App\Mail\BatchReservationPaidMail($batch));
                            $venueOwner = $batch->field->venue->owner;
                            if ($venueOwner?->email) {
                                \Illuminate\Support\Facades\Mail::to($venueOwner->email)
                                    ->send(new \App\Mail\VenueAdminBatchReservationMail($batch));
                            }
                        }
                        \Illuminate\Support\Facades\Log::info('Batch marcado PAID desde success URL', ['batch_id' => $batch->id]);
                        $batch->load(['field.venue', 'reservations' => fn($q) => $q->orderBy('start_at')]);
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Error verificando batch en success URL', ['batch_id' => $batch->id, 'error' => $e->getMessage()]);
            }
        }

        return view('batches.success', compact('batch'));
    })->name('batches.success');

    Route::get('/batch-pending/{batch}', function (\App\Models\ReservationBatch $batch) {
        abort_if($batch->user_id !== auth()->id() && auth()->user()->role !== 'super_admin', 403);
        return view('batches.pending', compact('batch'));
    })->name('batches.pending');

    Route::get('/batch-failure/{batch}', function (\App\Models\ReservationBatch $batch) {
        abort_if($batch->user_id !== auth()->id() && auth()->user()->role !== 'super_admin', 403);
        $batch->load(['field.venue']);
        return view('batches.failure', compact('batch'));
    })->name('batches.failure');

    Route::get('/batches/{batch}/status', function (\App\Models\ReservationBatch $batch) {
        abort_if($batch->user_id !== auth()->id() && auth()->user()->role !== 'super_admin', 403);
        return response()->json(['status' => $batch->status]);
    })->name('batches.status');

});

// Rutas de retorno de MercadoPago — requieren auth para proteger datos de la reserva
Route::middleware(['auth'])->group(function () {

Route::get('/reservation-success/{reservation}', function (\App\Models\Reservation $reservation, \Illuminate\Http\Request $request) {
    // Verificación de pertenencia
    $userId = auth()->id();
    $isOwner = $userId && ($reservation->user_id === $userId || optional(auth()->user())->role === 'super_admin');

    if (!$isOwner) {
        abort(403);
    }

    // Verificar y actualizar el pago usando el payment_id que MP manda en la URL
    if ($reservation->status === 'PENDING_PAYMENT') {
        $paymentId     = $request->query('payment_id') ?: $request->query('collection_id');
        $mpStatus      = $request->query('status') ?: $request->query('collection_status');

        if ($paymentId && $mpStatus === 'approved') {
            try {
                $reservation->loadMissing('field.venue');
                $accessToken = $reservation->field->venue->mp_access_token
                    ?? config('services.mercadopago.access_token');

                // Verificamos el pago directamente contra la API de MP con el ID recibido
                $mpResponse = \Illuminate\Support\Facades\Http::withOptions(['verify' => app()->isProduction()])
                    ->withToken($accessToken)
                    ->get("https://api.mercadopago.com/v1/payments/{$paymentId}");

                if ($mpResponse->successful() && $mpResponse->json('status') === 'approved') {
                    // Usamos lockForUpdate para evitar race condition con el webhook
                    $shouldSendEmails = false;
                    \Illuminate\Support\Facades\DB::transaction(function () use ($reservation, $paymentId, &$shouldSendEmails) {
                        $fresh = \App\Models\Reservation::lockForUpdate()->find($reservation->id);
                        // Solo procesamos si la reserva no fue ya marcada PAID por el webhook
                        if ($fresh && $fresh->status !== 'PAID') {
                            $fresh->update([
                                'status'              => 'PAID',
                                'payment_status'      => 'approved',
                                'payment_provider'    => 'mercadopago',
                                'payment_external_id' => (string) $paymentId,
                                'expires_at'          => null,
                            ]);
                            $shouldSendEmails = true;
                        }
                    });

                    if ($shouldSendEmails) {
                        $reservation->loadMissing(['user', 'field.venue.owner']);
                        \Illuminate\Support\Facades\Mail::to($reservation->user->email)
                            ->send(new \App\Mail\ReservationPaidMail($reservation));
                        $reservation->user->notify(new \App\Notifications\ReservationConfirmedNotification($reservation));
                        $venueOwner = $reservation->field->venue->owner;
                        if ($venueOwner?->email) {
                            \Illuminate\Support\Facades\Mail::to($venueOwner->email)
                                ->send(new \App\Mail\VenueAdminReservationMail($reservation));
                        }
                        \Illuminate\Support\Facades\Log::info('Reserva marcada PAID desde success URL', [
                            'reservation_id' => $reservation->id,
                            'payment_id'     => $paymentId,
                        ]);
                    }
                    $reservation->refresh();
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Error verificando pago en success URL', [
                    'reservation_id' => $reservation->id,
                    'error'          => $e->getMessage(),
                ]);
            }
        }
    }

    return view('reservations.success', compact('reservation'));
})->name('reservations.success');

Route::get('/reservation-pending/{reservation}', function (\App\Models\Reservation $reservation, \Illuminate\Http\Request $request) {
    $userId = auth()->id();
    $isOwner = $userId && ($reservation->user_id === $userId || optional(auth()->user())->role === 'super_admin');
    if (!$isOwner) {
        abort(403);
    }
    return view('reservations.pending', compact('reservation'));
})->name('reservations.pending');

Route::get('/reservation-failure/{reservation}', function (\App\Models\Reservation $reservation, \Illuminate\Http\Request $request) {
    $userId = auth()->id();
    $isOwner = $userId && ($reservation->user_id === $userId || optional(auth()->user())->role === 'super_admin');
    if (!$isOwner) {
        abort(403);
    }
    return view('reservations.failure', compact('reservation'));
})->name('reservations.failure');

}); // end Route::middleware(['auth']) for reservation return URLs

/*
|--------------------------------------------------------------------------
| Favorites + Reviews
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user'])->group(function () {
    Route::post('/venues/{venue}/favorite', [FavoriteVenueController::class, 'store'])
        ->name('venues.favorite');

    Route::post('/venues/{venue}/unfavorite', [FavoriteVenueController::class, 'destroy'])
        ->name('venues.unfavorite');

    Route::post('/venues/{venue}/reviews', [VenueReviewController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('venues.reviews.store');

    Route::get('/favorites', function () {
        $venues = auth()->user()
            ->favoriteVenues()
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderBy('name')
            ->get();

        return view('venues.favorites', compact('venues'));
    })->name('venues.favorites');
});

/*
|--------------------------------------------------------------------------
| Landing Contact Form
|--------------------------------------------------------------------------
*/

Route::post('/contact', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'email' => ['required', 'email', 'max:120'],
        'reason' => ['required', 'string', 'max:150'],
        'message' => ['required', 'string', 'max:2000'],
    ]);

    $text =
        "Nuevo contacto desde TuCancha\n\n" .
        "Nombre: {$data['name']}\n" .
        "Email: {$data['email']}\n" .
        "Motivo: {$data['reason']}\n\n" .
        "Mensaje:\n{$data['message']}";

    Mail::raw($text, function ($message) use ($data) {
        $message->to('tucancha10@gmail.com')
            ->subject('Nuevo contacto desde TuCancha')
            ->replyTo($data['email'], $data['name']);
    });
    // Nota: se usa Mail::raw sincrono porque no hay un Mailable dedicado.
    // Si se crea un Mailable, usar Mail::to(...)->queue(...) para mejor performance.

    return redirect()
        ->route('home')
        ->with('success', 'Tu consulta fue enviada correctamente.');
})->middleware('throttle:3,1')->name('contact.send');

/*
|--------------------------------------------------------------------------
| Venue Admin Panel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user', 'role:venue_admin,super_admin', 'venue.mp', 'venue.onboarding'])->prefix('va')->group(function () {

        // Onboarding
        Route::get('/onboarding/{step}', [\App\Http\Controllers\VenueAdmin\OnboardingController::class, 'show'])->where('step', '[1-4]')->name('va.onboarding.step');
        Route::post('/onboarding/venue', [\App\Http\Controllers\VenueAdmin\OnboardingController::class, 'storeVenue'])->name('va.onboarding.store_venue');
        Route::post('/onboarding/field', [\App\Http\Controllers\VenueAdmin\OnboardingController::class, 'storeField'])->name('va.onboarding.store_field');
        Route::post('/onboarding/schedule', [\App\Http\Controllers\VenueAdmin\OnboardingController::class, 'storeSchedule'])->name('va.onboarding.store_schedule');
        Route::post('/onboarding/complete', [\App\Http\Controllers\VenueAdmin\OnboardingController::class, 'complete'])->name('va.onboarding.complete');
        Route::post('/onboarding/skip', [\App\Http\Controllers\VenueAdmin\OnboardingController::class, 'skip'])->name('va.onboarding.skip');
        Route::post('/onboarding/resume', [\App\Http\Controllers\VenueAdmin\OnboardingController::class, 'resume'])->name('va.onboarding.resume');

        Route::get('/', [DashboardController::class, 'index'])->name('va.dashboard');

        // Geocode
        Route::get('/geocode', function(\Illuminate\Http\Request $request) {
            $address = $request->query('address');
            if (empty($address) || mb_strlen($address) > 300) {
                return response()->json(['status' => 'INVALID_REQUEST', 'results' => []], 400);
            }
            $key = config('services.google_geocoding.key');
            $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=' . $key;
            $response = \Illuminate\Support\Facades\Http::get($url);
            return response()->json($response->json());
        })->middleware('throttle:30,1')->name('va.geocode');

        // Venues
        Route::get('/venues/create', [VaVenueController::class, 'create'])->name('va.venues.create');
        Route::post('/venues', [VaVenueController::class, 'store'])->name('va.venues.store');
        Route::get('/venues/{venue}/connect-mp', [VaVenueController::class, 'connectMp'])->name('va.venues.connect_mp');
        Route::get('/venues/{venue}/edit', [VaVenueController::class, 'edit'])->name('va.venues.edit');
        Route::post('/venues/{venue}', [VaVenueController::class, 'update'])->name('va.venues.update');

        // Fields
        Route::get('/venues/{venue}/fields/create', [VaFieldController::class, 'create'])->name('va.fields.create');
        Route::post('/venues/{venue}/fields', [VaFieldController::class, 'store'])->name('va.fields.store');
        Route::get('/fields/{field}/edit', [VaFieldController::class, 'edit'])->name('va.fields.edit');
        Route::post('/fields/{field}', [VaFieldController::class, 'update'])->name('va.fields.update');
        Route::post('/fields/{field}/toggle-active', [VaFieldController::class, 'toggleActive'])->name('va.fields.toggle_active');
        Route::post('/fields/{field}/falta-uno-settings', [VaFaltaUnoSettingController::class, 'update'])->name('va.fields.falta_uno_settings');

        // Schedules
        Route::get('/fields/{field}/schedule', [VaScheduleController::class, 'edit'])->name('va.schedule.edit');
        Route::post('/fields/{field}/schedule', [VaScheduleController::class, 'update'])->name('va.schedule.update');

        // Reservations / agenda
        Route::get('/reservations', [VaReservationsController::class, 'index'])->name('va.reservations.index');
        Route::post('/reservations/{reservation}/cancel', [VaReservationsController::class, 'cancel'])->name('va.reservations.cancel');
        Route::post('/manual-reservations', [VaManualReservationController::class, 'store'])->name('va.reservations.manual_store');
        Route::post('/manual-recurring-reservations', [VaManualRecurringReservationController::class, 'store'])->name('va.reservations.manual_recurring_store');
        Route::get('/users/search', [VaManualReservationController::class, 'searchUsers'])->name('va.users.search');
        Route::get('/agenda', [VaReservationsController::class, 'agenda'])->name('va.reservations.agenda');

        // Blocks
        Route::get('/blocks', [VaFieldBlockController::class, 'index'])->name('va.blocks.index');
        Route::post('/blocks', [VaFieldBlockController::class, 'store'])->name('va.blocks.store');
        Route::post('/blocks/{block}/delete', [VaFieldBlockController::class, 'destroy'])->name('va.blocks.destroy');

        // Discounts
        Route::get('/discounts', [VaFieldDiscountController::class, 'index'])->name('va.discounts.index');
        Route::post('/discounts', [VaFieldDiscountController::class, 'store'])->name('va.discounts.store');
        Route::post('/discounts/{discount}/delete', [VaFieldDiscountController::class, 'destroy'])->name('va.discounts.destroy');

        // Reports
        Route::get('/reports', [VaReportsController::class, 'index'])->name('va.reports');
        Route::get('/reports/export', [VaReportsController::class, 'export'])->name('va.reports.export');

        // Venue payouts (what platform owes the venue) — hidden until referral system is re-enabled
        Route::get('/my-payouts', [VaVenuePayoutController::class, 'index'])->name('va.payouts.index')->middleware('role:super_admin');

        // Recurring discounts
        Route::post('/recurring-discounts', [VaFieldRecurringDiscountController::class, 'store'])->name('va.recurring_discounts.store');
        Route::post('/recurring-discounts/{recurringDiscount}/delete', [VaFieldRecurringDiscountController::class, 'destroy'])->name('va.recurring_discounts.destroy');

        // Mercado Pago OAuth
        Route::get('/venues/{venue}/mp-connect', [MercadoPagoOAuthController::class, 'redirect'])->name('va.mp_oauth.redirect');
        Route::post('/venues/{venue}/mp-disconnect', [MercadoPagoOAuthController::class, 'disconnect'])->name('va.mp_oauth.disconnect');

        // Checkin
        Route::get('/checkin', [VaCheckinController::class, 'index'])->name('va.checkin.index');
        Route::post('/checkin', [VaCheckinController::class, 'store'])->name('va.checkin.store');

        // Suscripciones recurrentes (abonos mensuales)
        Route::get('/suscripciones', [\App\Http\Controllers\VenueAdmin\RecurringSubscriptionAdminController::class, 'index'])->name('va.recurring_subscriptions.index');
        Route::post('/suscripciones/{subscription}/cancelar', [\App\Http\Controllers\VenueAdmin\RecurringSubscriptionAdminController::class, 'cancel'])->name('va.recurring_subscriptions.cancel');

        // Staff
        Route::get('/staff', [VaVenueStaffController::class, 'index'])->name('va.staff.index');
        Route::post('/staff/invite', [VaVenueStaffController::class, 'invite'])
            ->middleware('throttle:10,1')
            ->name('va.staff.invite');
        Route::post('/staff/remove', [VaVenueStaffController::class, 'remove'])->name('va.staff.remove');
        Route::post('/staff/cancel-invitation', [VaVenueStaffController::class, 'cancelInvitation'])->name('va.staff.cancel_invitation');
        Route::post('/staff/{staff}/permissions', [VaVenueStaffController::class, 'updatePermissions'])->name('va.staff.update_permissions');

        // Tournament settings for venue fields
        Route::get('/tournament-settings', [\App\Http\Controllers\VenueAdmin\TournamentSettingController::class, 'index'])->name('va.tournament_settings.index');
        Route::post('/tournament-settings/{field}', [\App\Http\Controllers\VenueAdmin\TournamentSettingController::class, 'update'])->name('va.tournament_settings.update');

        // Tournament venue requests
        Route::get('/tournament-requests', [\App\Http\Controllers\VenueAdmin\TournamentRequestController::class, 'index'])->name('va.tournament_requests.index');
        Route::post('/tournament-requests/{tournamentRequest}/approve', [\App\Http\Controllers\VenueAdmin\TournamentRequestController::class, 'approve'])->name('va.tournament_requests.approve');
        Route::post('/tournament-requests/{tournamentRequest}/reject', [\App\Http\Controllers\VenueAdmin\TournamentRequestController::class, 'reject'])->name('va.tournament_requests.reject');
        Route::post('/tournament-requests/{tournamentRequest}/mensaje', [\App\Http\Controllers\TournamentRequestChatController::class, 'storeVenueAdmin'])->name('va.tournament_requests.message');
    });

    Route::get('/mp-oauth/callback', [MercadoPagoOAuthController::class, 'callback'])
    ->middleware(['auth', 'active.user'])
    ->name('va.mp_oauth.callback');

    Route::get('/mp-oauth/organizer-callback', [OrganizerMercadoPagoOAuthController::class, 'callback'])
    ->middleware(['auth', 'active.user'])
    ->name('organizador.mp_oauth.callback');

/*
|--------------------------------------------------------------------------
| Organizador — Suscripcion
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user'])->group(function () {
    Route::get('/organizador/planes', [OrganizerSubscriptionController::class, 'plans'])->name('organizador.planes');
    Route::post('/organizador/suscribir', [OrganizerSubscriptionController::class, 'subscribe'])->name('organizador.subscribe');
    Route::post('/organizador/cancelar-suscripcion', [OrganizerSubscriptionController::class, 'cancel'])->name('organizador.cancel');
    Route::get('/organizador/suscripcion/exito', [OrganizerSubscriptionController::class, 'success'])->name('organizador.success');

    // Organizer MercadoPago OAuth
    Route::get('/organizador/conectar-mp', [OrganizerMercadoPagoOAuthController::class, 'redirect'])->name('organizador.mp_oauth.redirect');
    Route::post('/organizador/desconectar-mp', [OrganizerMercadoPagoOAuthController::class, 'disconnect'])->name('organizador.mp_oauth.disconnect');
});

/*
|--------------------------------------------------------------------------
| Torneos
|--------------------------------------------------------------------------
*/

Route::get('/torneos', [TournamentController::class, 'index'])->name('torneos.index');

Route::middleware(['auth', 'active.user'])->group(function () {
    // Mis torneos (debe ir antes de {tournament})
    Route::get('/torneos/mis-torneos', [TournamentController::class, 'myTournaments'])->name('torneos.my');

    // Organizador — crear y gestionar torneos
    Route::get('/torneos/crear', [TournamentOrganizerController::class, 'create'])->name('torneos.create');
    Route::post('/torneos', [TournamentOrganizerController::class, 'store'])->middleware('throttle:10,1')->name('torneos.store');
    Route::get('/torneos/{tournament}/editar', [TournamentOrganizerController::class, 'edit'])->name('torneos.edit');
    Route::put('/torneos/{tournament}', [TournamentOrganizerController::class, 'update'])->name('torneos.update');
    Route::get('/torneos/{tournament}/gestionar', [TournamentOrganizerController::class, 'manage'])->name('torneos.manage');
    Route::post('/torneos/{tournament}/publicar', [TournamentOrganizerController::class, 'publish'])->name('torneos.publish');
    Route::post('/torneos/{tournament}/cerrar-inscripcion', [TournamentOrganizerController::class, 'closeRegistration'])->name('torneos.close_registration');
    Route::post('/torneos/{tournament}/cancelar', [TournamentOrganizerController::class, 'cancel'])->name('torneos.cancel');
    Route::post('/torneos/{tournament}/generar-fixture', [TournamentOrganizerController::class, 'generateFixture'])->name('torneos.generate_fixture');
    Route::post('/torneos/{tournament}/partidos/{match}/resultado', [TournamentOrganizerController::class, 'updateMatchResult'])->name('torneos.match_result');

    // Buscar y solicitar cancha de TuCancha
    Route::get('/torneos/{tournament}/buscar-cancha', [TournamentOrganizerController::class, 'searchVenue'])->name('torneos.search_venue');
    Route::post('/torneos/{tournament}/solicitar-cancha', [TournamentOrganizerController::class, 'requestVenue'])->name('torneos.request_venue');

    // Chat de solicitud de cancha (organizador)
    Route::post('/torneos/solicitudes/{tournamentRequest}/mensaje', [\App\Http\Controllers\TournamentRequestChatController::class, 'storeOrganizer'])->name('torneos.request_message');
    Route::post('/torneos/solicitudes/{tournamentRequest}/leido', [\App\Http\Controllers\TournamentRequestChatController::class, 'markRead'])->name('torneos.request_chat_read');

    // Equipos — inscripcion y gestion por capitan
    Route::get('/torneos/{tournament}/inscribir', [TournamentTeamController::class, 'create'])->name('torneos.teams.create');
    Route::post('/torneos/{tournament}/equipos', [TournamentTeamController::class, 'store'])->middleware('throttle:10,1')->name('torneos.teams.store');
    Route::get('/torneos/{tournament}/equipos/{team}', [TournamentTeamController::class, 'show'])->name('torneos.teams.show');
    Route::post('/torneos/{tournament}/equipos/{team}/retirar', [TournamentTeamController::class, 'withdraw'])->name('torneos.teams.withdraw');

    // Tournament payment routes (payment is pre-team)
    Route::get('/torneos/{tournament}/pagar-inscripcion', [TournamentPaymentController::class, 'checkout'])->name('torneos.teams.checkout');
    Route::post('/torneos/{tournament}/pagar-inscripcion', [TournamentPaymentController::class, 'pay'])->name('torneos.teams.pay');
    Route::get('/torneos/{tournament}/pago-retorno', [TournamentPaymentController::class, 'paymentReturn'])->name('torneos.teams.payment-return');
    Route::post('/torneos/{tournament}/equipos/{team}/confirmar-pago', [TournamentPaymentController::class, 'confirmPayment'])->name('torneos.teams.confirm_payment');

    // Gestion de equipos por organizador
    Route::post('/torneos/{tournament}/equipos/{team}/descalificar', [TournamentTeamManageController::class, 'disqualify'])->name('torneos.teams.disqualify');
});

// Show publico — AL FINAL para que "crear", "inscribir", etc. no se capturen como slug
Route::get('/torneos/{tournament}', [TournamentController::class, 'show'])->name('torneos.show');

/*
|--------------------------------------------------------------------------
| Super Admin - User Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user', 'role:super_admin'])->group(function () {
    Route::get('/sa/users', [UserManagementController::class, 'index'])
        ->name('sa.users.index');

    Route::post('/sa/users/{user}/role', [UserManagementController::class, 'updateRole'])
        ->name('sa.users.role');

    Route::post('/sa/users/{user}/delete', [UserManagementController::class, 'destroy'])
        ->name('sa.users.destroy');
});

/*
|--------------------------------------------------------------------------
| Super Admin - System Messages
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user', 'role:super_admin'])->group(function () {
    Route::get('/sa/messages', [SystemMessageController::class, 'index'])
        ->name('sa.messages.index');

    Route::post('/sa/messages', [SystemMessageController::class, 'store'])
        ->name('sa.messages.store');
        
    Route::post('/sa/settings/membership-price', [SystemMessageController::class, 'updateMembershipPrice'])
        ->name('sa.settings.membership_price');

    Route::post('/sa/messages/{message}/toggle', [SystemMessageController::class, 'toggle'])
        ->name('sa.messages.toggle');

    Route::post('/sa/messages/{message}/delete', [SystemMessageController::class, 'destroy'])
        ->name('sa.messages.destroy');

    Route::post('/sa/users/{user}/deactivate',
        [UserManagementController::class, 'deactivate']
    )->name('sa.users.deactivate');

    Route::post('/sa/users/{user}/activate',
        [UserManagementController::class, 'activate']
    )->name('sa.users.activate');

    Route::post('/sa/users/{user}/impersonate',
        [UserManagementController::class, 'impersonate']
    )->name('sa.users.impersonate');


    Route::get('/sa/plans', [MembershipPlanController::class, 'index'])
        ->name('sa.plans.index');

    Route::post('/sa/plans/{plan}', [MembershipPlanController::class, 'update'])
        ->name('sa.plans.update');

    Route::get('/sa/organizer-plans', [OrganizerPlanController::class, 'index'])
        ->name('sa.organizer_plans.index');

    Route::post('/sa/organizer-plans/{plan}', [OrganizerPlanController::class, 'update'])
        ->name('sa.organizer_plans.update');

    // SA payouts — hidden until referral system is re-enabled
    /*
    Route::get('/sa/payouts', [PlatformPayoutController::class, 'index'])
        ->name('sa.payouts.index');

    Route::post('/sa/payouts/{payout}/mark-paid', [PlatformPayoutController::class, 'markPaid'])
        ->name('sa.payouts.mark_paid');

    Route::post('/sa/payouts/venue/{venue}/mark-all-paid', [PlatformPayoutController::class, 'markVenuePaid'])
        ->name('sa.payouts.mark_venue_paid');

    Route::post('/sa/payouts/venue/{venue}/pay-via-mp', [PlatformPayoutController::class, 'payViaMP'])
        ->name('sa.payouts.pay_via_mp');

    Route::get('/sa/payouts/venue/{venue}/success', function (\App\Models\Venue $venue) {
        return redirect()->route('sa.payouts.index')
            ->with('success', "Pago a {$venue->name} procesado correctamente por Mercado Pago.");
    })->name('sa.payouts.venue_success');

    Route::get('/sa/payouts/venue/{venue}/failure', function (\App\Models\Venue $venue) {
        return redirect()->route('sa.payouts.index')
            ->with('error', "El pago a {$venue->name} fue rechazado o cancelado. Intentá de nuevo.");
    })->name('sa.payouts.venue_failure');

    Route::get('/sa/payouts/venue/{venue}/pending', function (\App\Models\Venue $venue) {
        return redirect()->route('sa.payouts.index')
            ->with('success', "El pago a {$venue->name} está pendiente de acreditación. El estado se actualizará automáticamente via webhook.");
    })->name('sa.payouts.venue_pending');
    */

    // Blog posts CRUD
    Route::resource('/sa/blog-posts', BlogPostController::class)->except(['show'])
        ->names([
            'index'   => 'blog-posts.index',
            'create'  => 'blog-posts.create',
            'store'   => 'blog-posts.store',
            'edit'    => 'blog-posts.edit',
            'update'  => 'blog-posts.update',
            'destroy' => 'blog-posts.destroy',
        ]);
});

// Ruta para salir de impersonación — protegida solo con auth (el super_admin
// ya no tiene ese rol mientras impersona a otro usuario)
Route::post('/super-admin/stop-impersonate', [UserManagementController::class, 'stopImpersonate'])
    ->middleware(['auth', 'active.user'])
    ->name('sa.users.stop_impersonate');

// Solo devuelve el status; el usuario debe ser dueño de la reserva o super_admin.
// Si no está autenticado (ej: regresó de MP sin sesión activa) devuelve 401
// para que el frontend muestre mensaje apropiado.
Route::get('/reservations/{reservation}/status', function (\App\Models\Reservation $reservation) {
    $user = auth()->user();

    if (!$user) {
        return response()->json(['error' => 'unauthenticated'], 401);
    }

    if ($reservation->user_id !== $user->id && $user->role !== 'super_admin') {
        return response()->json(['error' => 'forbidden'], 403);
    }

    return response()->json(['status' => $reservation->status]);
})->middleware('auth');

require __DIR__ . '/auth.php';
