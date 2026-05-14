----------DIA 1---------
PASO1:
El proyecto tiene muchas carpetas diferentes. Diría que las mas grandes son routes, resources y app. La logica principal esta en app a mi parecer, ya que alli se manejan los eventos, los controladores, etc.

PASO2:
La carpeta routes/web.php tiene mcuhas rutas.
-/ es la pagina de bienvenida (la principal)
-/feedback es la ruta del botoncito de feedback que aparece abajo a la derecha
-/auth/google creo que es la ruta de registro
-/auth/google/callback creo que es la ruta de inicio de sesion
-/como-funciona es la de la vista de como funciona
-/dashboard es el panel admnin
-Route::middleware(['auth', 'active.user'])->group(function () {
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

esa parte no la entiendo la verdad, entiendo que las vistas que son, por ejemplo la del perfil, algunnos pagos, pero no entiendo cosas como la parte de arriba del todo lo del middleware, es decir, todas esas vistas estan dentro del middleware pero no se exactamente que es. Tampoco entiendo bien lo de system-messages, no se que vista puede ser. Tampoco entiendo muy bien cual es la diferencia entre get y post. Despues tampoco entiendo muy bien Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit'); no entiendo la estructura y que implica/significa cada cosa.

Luego no se lo que es el sitemap.
Route::get('/venues', [VenueController::class, 'index'])->name('venues.index');
Route::get('/venues/{venue}', [VenueController::class, 'show'])->name('venues.show');

No entiendo la diferencia entre esas dos rutas por ejemplo. Entiendo que una es venues que la vista de los complejos en general y otra es de un complejo especifico, pero no se si entenderé bien, pero primero siempre pasa por un controller que no se exactamente que valida. Tampoco entiendo que significa lo de index o show.
No se cual es la diferencia entre route get o route post.

Despues el resto de vistas creo entenderlas bien, al menos que ruta es cada vista.

La unica que no entiendo nada es la de mercado pago
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

tampoco entiendo el codigo en si que hace