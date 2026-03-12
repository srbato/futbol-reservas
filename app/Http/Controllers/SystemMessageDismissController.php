<?php

namespace App\Http\Controllers;

use App\Models\SystemMessage;
use Illuminate\Http\Request;

class SystemMessageDismissController extends Controller
{
    public function store(Request $request, SystemMessage $message)
    {
        if (!$request->user()) {
            abort(403);
        }

        $message->dismissedByUsers()->syncWithoutDetaching([$request->user()->id]);

        return response()->json(['ok' => true]);
    }
}