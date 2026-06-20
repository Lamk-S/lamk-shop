<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_usuarios', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = User::query()
            ->with(['roles:id,name'])
            ->withTrashed()
            ->withCount('roles')
            ->orderBy('name');

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('roles', function ($qr) use ($search) {
                        $qr->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($qr) use ($request) {
                $qr->where('name', $request->input('role'));
            });
        }

        if ($request->filled('estado')) {
            if ($request->input('estado') === 'activo') {
                $query->where('estado', 1)->whereNull('deleted_at');
            } elseif ($request->input('estado') === 'inactivo') {
                $query->where(function ($q) {
                    $q->where('estado', 0)->orWhereNotNull('deleted_at');
                });
            }
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $users = $query->paginate($perPage)->withQueryString();

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('user.index', compact('users', 'roles', 'perPage'));
    }

    public function create()
    {
        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('user.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => bcrypt($data['password']),
                    'estado' => $data['estado'] ?? 1,
                ]);

                $user->syncRoles([$data['role']]);
            });

            return redirect()
                ->route('users.index')
                ->with('success', 'Usuario registrado y aprovisionado correctamente');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al crear el usuario: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(User $user)
    {
        $user->load('roles:id,name');

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('user.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data, $user) {
                $payload = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'estado' => $data['estado'],
                ];

                if (!empty($data['password'])) {
                    $payload['password'] = bcrypt($data['password']);
                }

                $user->update($payload);
                $user->syncRoles([$data['role']]);
            });

            return redirect()
                ->route('users.index')
                ->with('success', 'Perfil de usuario actualizado correctamente');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al actualizar el usuario: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(User $user)
    {
        try {
            if (Auth::id() === $user->id) {
                return back()->withErrors(['error' => 'Acción denegada: No puedes desactivar tu propio usuario en la sesión actual.']);
            }

            if ($user->hasRole('administrador')) {
                $adminCount = User::query()
                    ->role('administrador')
                    ->whereKeyNot($user->id)
                    ->whereNull('deleted_at')
                    ->where('estado', 1)
                    ->count();

                if ($adminCount === 0) {
                    return back()->withErrors(['error' => 'Protección del sistema: No puedes eliminar al único administrador activo.']);
                }
            }

            if ($user->trashed()) {
                $user->restore();
                $user->update(['estado' => 1]);
                $message = 'Cuenta de usuario reactivada correctamente';
            } else {
                $user->delete();
                $user->update(['estado' => 0]);
                $message = 'Cuenta de usuario suspendida y enviada a la papelera';
            }

            return redirect()->route('users.index')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al procesar la solicitud: ' . $e->getMessage()]);
        }
    }
}