<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SystemMessage;
use App\Models\User;
use App\Models\VenueAdminSubscription;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $q = trim((string) $request->query('q', ''));
        $role = trim((string) $request->query('role', ''));

        $users = User::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('name', 'like', "%{$q}%")
                       ->orWhere('email', 'like', "%{$q}%");
                });
            })
            ->when($role !== '', function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('sa.users.index', compact('users', 'q', 'role'));
    }

    public function updateRole(Request $request, User $user)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $data = $request->validate([
            'role' => ['required', 'in:user,venue_admin,super_admin'],
        ]);

        $oldRole = $user->role;
        $newRole = $data['role'];

        $user->role = $newRole;
        $user->save();

        // Otorgar acceso especial sin vencimiento al pasar a venue_admin
        if ($newRole === 'venue_admin' && $oldRole !== 'venue_admin') {
            $hasActive = $user->activeVenueAdminSubscription()->exists();

            if (!$hasActive) {
                VenueAdminSubscription::create([
                    'user_id'          => $user->id,
                    'plan_slug'        => 'special',
                    'billing_cycle'    => 'monthly',
                    'long_term_months' => 1,
                    'status'           => 'ACTIVE',
                    'monthly_price'    => 0,
                    'currency'         => 'ARS',
                    'payment_provider' => null,
                    'starts_at'        => now(),
                    'expires_at'       => now()->addYears(100),
                ]);
            }
        }

        // Al quitar el rol de venue_admin, cancelar suscripciones activas
        if ($oldRole === 'venue_admin' && $newRole !== 'venue_admin') {
            $user->venueAdminSubscriptions()
                ->whereIn('status', ['ACTIVE', 'TRIAL'])
                ->update(['status' => 'CANCELLED', 'expires_at' => now()]);
        }

        return back()->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        if ($request->user()->id === $user->id) {
            return back()->with('error', 'No podés eliminar tu propio usuario.');
        }

        SystemMessage::where('target_user_id', $user->id)->delete();

        $user->delete();

        return back()->with('success', 'Usuario eliminado correctamente.');
    }

    public function deactivate(Request $request, User $user)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        if ($request->user()->id === $user->id) {
            return back()->with('error', 'No podés desactivar tu propio usuario.');
        }

        $user->is_active = false;
        $user->save();

        return back()->with('success', 'Usuario desactivado.');
    }

    public function activate(Request $request, User $user)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        $user->is_active = true;
        $user->save();

        return back()->with('success', 'Usuario activado.');
    }

    public function impersonate(Request $request, User $user)
    {
        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        if ($request->user()->id === $user->id) {
            return back()->with('error', 'No podés impersonarte a vos mismo.');
        }

        $originalAdminId = $request->user()->id;

        auth()->login($user);

        session([
            'impersonating_as'  => $user->id,
            'original_admin_id' => $originalAdminId,
        ]);

        return redirect('/');
    }

    public function stopImpersonate(Request $request)
    {
        $originalAdminId = session('original_admin_id');

        if (!$originalAdminId) {
            return redirect('/');
        }

        $admin = User::find($originalAdminId);

        if (!$admin || $admin->role !== 'super_admin') {
            abort(403);
        }

        auth()->login($admin);

        session()->forget(['impersonating_as', 'original_admin_id']);

        return redirect()->route('sa.users.index')
            ->with('success', 'Impersonación finalizada.');
    }
}