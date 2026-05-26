<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-user|crear-user|editar-user|eliminar-user', only: ['index']),
            new Middleware('permission:crear-user', only: ['create', 'store']),
            new Middleware('permission:editar-user', only: ['edit', 'update']),
            new Middleware('permission:eliminar-user', only: ['destroy']),
        ];
    }

    public function index()
    {
        $users = User::with('roles')->withTrashed()->get();
        return view('user.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('user.create', compact('roles'));
    }

    public function store(StoreUserRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password,
                    'estado' => 1,
                ]);

                $user->assignRole($request->role);
            });

            return redirect()->route('users.index')->with('success', 'Usuario registrado');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al crear el usuario: ' . $e->getMessage()]);
        }
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $user->load('roles');

        return view('user.edit', compact('user', 'roles'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            DB::transaction(function () use ($request, $user) {
                $data = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'estado' => $request->estado ?? $user->estado,
                ];

                if ($request->filled('password')) {
                    $data['password'] = $request->password;
                }

                $user->update($data);
                $user->syncRoles($request->role);
            });

            return redirect()->route('users.index')->with('success', 'Usuario actualizado');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar el usuario: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);

            if ($user->trashed()) {
                $user->restore();
                $message = 'Usuario restaurado';
            } else {
                $user->delete();
                $message = 'Usuario eliminado';
            }

            return redirect()->route('users.index')->with('success', $message);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al modificar el usuario: ' . $e->getMessage()]);
        }
    }
}