<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        $user->role = $data['role'];
        $user->save();

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
}