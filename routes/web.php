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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

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
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
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
});

/*
|--------------------------------------------------------------------------
| Mercado Pago Webhook + Return Screens
|--------------------------------------------------------------------------
*/

Route::post('/webhooks/mercadopago', [MercadoPagoWebhookController::class, 'handle'])
    ->name('webhooks.mercadopago');

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
        $message->to('tucancha@gmail.com')
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
    });

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
});

require __DIR__ . '/auth.php';
