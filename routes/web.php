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
use App\Http\Controllers\RecurringReservationController;
use App\Http\Controllers\ReservationBatchCheckoutController;
use App\Http\Controllers\ReservationBatchMercadoPagoController;
use App\Http\Controllers\VenueAdmin\FieldRecurringDiscountController as VaFieldRecurringDiscountController;
use App\Http\Controllers\ReservationViewController;
use App\Http\Controllers\SystemMessageDismissController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\VenueReviewController;
use App\Http\Controllers\SuperAdmin\SystemMessageController;
use App\Http\Controllers\SuperAdmin\UserManagementController;
use App\Http\Controllers\VenueAdmin\CheckinController as VaCheckinController;
use App\Http\Controllers\VenueAdmin\DashboardController;
use App\Http\Controllers\VenueAdmin\FieldBlockController as VaFieldBlockController;
use App\Http\Controllers\VenueAdmin\FieldController as VaFieldController;
use App\Http\Controllers\VenueAdmin\FieldDiscountController as VaFieldDiscountController;
use App\Http\Controllers\VenueAdmin\ReportsController as VaReportsController;
use App\Http\Controllers\VenueAdmin\ReservationsController as VaReservationsController;
use App\Http\Controllers\VenueAdmin\ScheduleController as VaScheduleController;
use App\Http\Controllers\VenueAdmin\VenueController as VaVenueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VenueAdminMembershipController;
use App\Http\Controllers\MercadoPagoOAuthController;
use App\Http\Controllers\SuperAdmin\MembershipPlanController;
use App\Http\Controllers\ReferralController;
use App\Models\MembershipPlan;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/como-funciona', function () {
    return view('como-funciona');
})->name('como-funciona');

Route::get('/nosotros', function () {
    return view('nosotros');
})->name('nosotros');

Route::get('/planes', function () {
    $plans = MembershipPlan::where('is_active', true)->orderBy('sort_order')->get();
    return view('planes', compact('plans'));
})->name('planes');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Auth / Profile
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user'])->group(function () {
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
});

/*
|--------------------------------------------------------------------------
| Public Venues / Fields
|--------------------------------------------------------------------------
*/

Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
Route::get('/venues/{venue}', [VenueController::class, 'show'])->name('venues.show');

Route::get('/fields/{field}', [FieldController::class, 'show'])->name('fields.show');
Route::get('/fields/{field}/availability', [AvailabilityController::class, 'show'])->name('fields.availability');

/*
|--------------------------------------------------------------------------
| Reservations
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user'])->group(function () {
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::post('/reservations/recurring', [RecurringReservationController::class, 'store'])->name('reservations.recurring');

    Route::get('/batches/{batch}/checkout', [ReservationBatchCheckoutController::class, 'show'])->name('batches.checkout');
    Route::post('/batches/{batch}/mercadopago', [ReservationBatchMercadoPagoController::class, 'checkout'])->name('batches.mercadopago');

    Route::get('/reservations/{reservation}', [ReservationViewController::class, 'show'])
        ->name('reservations.show');

    Route::get('/reservations/{reservation}/checkout', [CheckoutController::class, 'show'])
        ->name('reservations.checkout');

    Route::post('/reservations/{reservation}/pay-dev', [PaymentDevController::class, 'pay'])
        ->name('reservations.pay_dev');

    Route::post('/reservations/{reservation}/mercadopago', [MercadoPagoController::class, 'checkout'])
        ->name('reservations.mercadopago');

    Route::get('/my-reservations', [MyReservationsController::class, 'index'])
        ->name('my_reservations');

    Route::post('/reservations/{reservation}/cancel', [ReservationCancelController::class, 'cancelByUser'])
        ->name('reservations.cancel');

    Route::get('/referral', [ReferralController::class, 'index'])->name('referral.index');
    Route::post('/referral/redeem-reservation/{reservation}', [ReferralController::class, 'redeemReservation'])->name('referral.redeem_reservation');
    Route::post('/referral/redeem-month/{reward}', [ReferralController::class, 'redeemMonth'])->name('referral.redeem_month');
});

/*
|--------------------------------------------------------------------------
| Mercado Pago Webhook + Return Screens
|--------------------------------------------------------------------------
*/

Route::post('/webhooks/mercadopago', [MercadoPagoWebhookController::class, 'handle'])
    ->name('webhooks.mercadopago');

Route::get('/batch-success/{batch}', function (\App\Models\ReservationBatch $batch) {
    $batch->load(['field.venue', 'reservations' => fn($q) => $q->orderBy('start_at')]);
    return view('batches.success', compact('batch'));
})->name('batches.success');

Route::get('/batch-pending/{batch}', function (\App\Models\ReservationBatch $batch) {
    return view('batches.pending', compact('batch'));
})->name('batches.pending');

Route::get('/batch-failure/{batch}', function (\App\Models\ReservationBatch $batch) {
    $batch->load(['field.venue']);
    return view('batches.failure', compact('batch'));
})->name('batches.failure');

Route::get('/batches/{batch}/status', function (\App\Models\ReservationBatch $batch) {
    $user = auth()->user();
    if (!$user) return response()->json(['error' => 'unauthenticated'], 401);
    if ($batch->user_id !== $user->id && $user->role !== 'super_admin') {
        return response()->json(['error' => 'forbidden'], 403);
    }
    return response()->json(['status' => $batch->status]);
})->middleware('auth')->name('batches.status');

Route::get('/reservation-success/{reservation}', function (\App\Models\Reservation $reservation) {
    return view('reservations.success', compact('reservation'));
})->name('reservations.success');

Route::get('/reservation-pending/{reservation}', function (\App\Models\Reservation $reservation) {
    return view('reservations.pending', compact('reservation'));
})->name('reservations.pending');

Route::get('/reservation-failure/{reservation}', function (\App\Models\Reservation $reservation) {
    return view('reservations.failure', compact('reservation'));
})->name('reservations.failure');

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

    return redirect()
        ->route('home')
        ->with('success', 'Tu consulta fue enviada correctamente.');
})->name('contact.send');

/*
|--------------------------------------------------------------------------
| Venue Admin Panel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user', 'role:venue_admin,super_admin'])->prefix('va')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('va.dashboard');

        // Venues
        Route::get('/venues/create', [VaVenueController::class, 'create'])->name('va.venues.create');
        Route::post('/venues', [VaVenueController::class, 'store'])->name('va.venues.store');
        Route::get('/venues/{venue}/edit', [VaVenueController::class, 'edit'])->name('va.venues.edit');
        Route::post('/venues/{venue}', [VaVenueController::class, 'update'])->name('va.venues.update');

        // Fields
        Route::get('/venues/{venue}/fields/create', [VaFieldController::class, 'create'])->name('va.fields.create');
        Route::post('/venues/{venue}/fields', [VaFieldController::class, 'store'])->name('va.fields.store');
        Route::get('/fields/{field}/edit', [VaFieldController::class, 'edit'])->name('va.fields.edit');
        Route::post('/fields/{field}', [VaFieldController::class, 'update'])->name('va.fields.update');
        Route::post('/fields/{field}/toggle-active', [VaFieldController::class, 'toggleActive'])->name('va.fields.toggle_active');

        // Schedules
        Route::get('/fields/{field}/schedule', [VaScheduleController::class, 'edit'])->name('va.schedule.edit');
        Route::post('/fields/{field}/schedule', [VaScheduleController::class, 'update'])->name('va.schedule.update');

        // Reservations / agenda
        Route::get('/reservations', [VaReservationsController::class, 'index'])->name('va.reservations.index');
        Route::post('/reservations/{reservation}/cancel', [VaReservationsController::class, 'cancel'])->name('va.reservations.cancel');
        Route::get('/agenda', [VaReservationsController::class, 'agenda'])->name('va.reservations.agenda');

        // Check-in por código
        Route::get('/checkin', [VaCheckinController::class, 'index'])->name('va.checkin');
        Route::post('/checkin', [VaCheckinController::class, 'store'])->name('va.checkin.store');

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

        // Recurring discounts
        Route::post('/recurring-discounts', [VaFieldRecurringDiscountController::class, 'store'])->name('va.recurring_discounts.store');
        Route::post('/recurring-discounts/{recurringDiscount}/delete', [VaFieldRecurringDiscountController::class, 'destroy'])->name('va.recurring_discounts.destroy');

        // Mercado Pago OAuth
        Route::get('/venues/{venue}/mp-connect', [MercadoPagoOAuthController::class, 'redirect'])->name('va.mp_oauth.redirect');
        Route::post('/venues/{venue}/mp-disconnect', [MercadoPagoOAuthController::class, 'disconnect'])->name('va.mp_oauth.disconnect');
    
    });

    Route::get('/mp-oauth/callback', [MercadoPagoOAuthController::class, 'callback'])
    ->middleware(['auth', 'active.user'])
    ->name('va.mp_oauth.callback');

/*
|--------------------------------------------------------------------------
| Super Admin - User Management
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'active.user'])->group(function () {
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

Route::middleware(['auth', 'active.user'])->group(function () {
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

    Route::post('/system-messages/{message}/dismiss', [SystemMessageDismissController::class, 'store'])
        ->name('system-messages.dismiss');

    Route::post('/sa/users/{user}/deactivate',
        [UserManagementController::class, 'deactivate']
    )->name('sa.users.deactivate');

    Route::post('/sa/users/{user}/activate',
        [UserManagementController::class, 'activate']
    )->name('sa.users.activate');

    Route::get('/sa/plans', [MembershipPlanController::class, 'index'])
        ->name('sa.plans.index');

    Route::post('/sa/plans/{plan}', [MembershipPlanController::class, 'update'])
        ->name('sa.plans.update');
});

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
