<?php

namespace App\Http\Controllers;

use App\Models\ReservationBatch;
use Illuminate\Http\Request;

class ReservationBatchCheckoutController extends Controller
{
    public function show(Request $request, ReservationBatch $batch)
    {
        $user = $request->user();

        if ($batch->user_id !== $user->id && $user->role !== 'super_admin') {
            abort(403);
        }

        $batch->load(['field.venue', 'reservations' => function ($q) {
            $q->orderBy('start_at');
        }]);

        return view('batches.checkout', compact('batch'));
    }
}
