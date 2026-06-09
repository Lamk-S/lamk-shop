<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_usuarios', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $users = User::with(['roles'])
            ->withTrashed()
            ->latest('id')
            ->get();

        return view('user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

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
                    'password' => $data['password'],
                    'estado' => $data['estado'] ?? 1,
                ]);

                $user->assignRole($data['role']);
            });

            return redirect()
                ->route('users.index')
                ->with('success', 'Usuario registrado correctamente');
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al crear el usuario: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function edit(User $user)
    {
        $roles = Role::where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        $user->load('roles');

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
                    $payload['password'] = $data['password'];
                }

                $user->update($payload);
                $user->syncRoles([$data['role']]);
            });

            return redirect()
                ->route('users.index')
                ->with('success', 'Usuario actualizado correctamente');
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al actualizar el usuario: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function destroy(string $id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            if (Auth::id() === $user->id) {
                return back()->withErrors([
                    'error' => 'No puedes eliminar tu propio usuario mientras estás autenticado.',
                ]);
            }

            if ($user->trashed()) {
                $user->restore();
                $message = 'Usuario restaurado correctamente';
            } else {
                $user->delete();
                $message = 'Usuario eliminado correctamente';
            }

            return redirect()
                ->route('users.index')
                ->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors([
                'error' => 'Error al modificar el usuario: ' . $e->getMessage(),
            ]);
        }
    }
}