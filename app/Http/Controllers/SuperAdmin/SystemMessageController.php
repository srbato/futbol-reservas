<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Models\SystemMessage;
use App\Models\User;
use Illuminate\Http\Request;

class SystemMessageController extends Controller
{
    private const DEFAULT_MEMBERSHIP_PRICE = 29990;

    public function index(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $messages = SystemMessage::query()
            ->with('targetUser')
            ->latest()
            ->get();

        $users = User::query()
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $membershipPrice = (float) PlatformSetting::getValue(
            'venue_admin_membership_monthly_price',
            self::DEFAULT_MEMBERSHIP_PRICE
        );

        return view('sa.messages.index', compact('messages', 'users', 'membershipPrice'));
    }

    public function updateMembershipPrice(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $data = $request->validate([
            'membership_price' => ['required', 'numeric', 'min:1'],
        ]);

        PlatformSetting::setValue(
            'venue_admin_membership_monthly_price',
            $data['membership_price']
        );

        return back()->with('success', 'Precio de membresía actualizado correctamente.');
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'body' => ['required', 'string', 'max:3000'],
            'target_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        SystemMessage::create([
            'title' => $data['title'],
            'body' => $data['body'],
            'target_user_id' => $data['target_user_id'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Mensaje creado correctamente.');
    }

    public function toggle(Request $request, SystemMessage $message)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $message->is_active = !$message->is_active;
        $message->save();

        return back()->with('success', 'Estado del mensaje actualizado.');
    }

    public function destroy(Request $request, SystemMessage $message)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $message->delete();

        return back()->with('success', 'Mensaje eliminado.');
    }
}