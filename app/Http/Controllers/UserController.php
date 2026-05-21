<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return view('user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            DB::beginTransaction();
            // Encriptar la contraseña
            $fieldHash = Hash::make($request->password);
            // Modificar el valor de password en el request
            $request->merge(['password' => $fieldHash]);
            // Crear el usuario
            $user = User::create($request->only('name', 'email', 'password'));
            // Asignar el rol al usuario
            $user->assignRole($request->role);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear el usuario: ' . $e->getMessage()]);
        }
        return redirect()->route('users.index')->with('success', 'Usuario registrado');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('user.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            DB::beginTransaction();
            // Encriptar la contraseña si se ha proporcionado una nueva
            if ($request->filled('password')) {
                $fieldHash = Hash::make($request->password);
                $request->merge(['password' => $fieldHash]);
            } else {
                // Si no se ha proporcionado una nueva contraseña, eliminar el campo para evitar que se actualice a null
                $request->request->remove('password');
            }
            // Actualizar el usuario
            $user->update($request->only('name', 'email', 'password'));
            // Sincronizar el rol del usuario
            $user->syncRoles($request->role);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar el usuario: ' . $e->getMessage()]);
        }
        return redirect()->route('users.index')->with('success', 'Usuario actualizado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        // Eliminar roles asociados al usuario
        $rolUser = $user->getRoleNames()->first();
        $user->removeRole($rolUser);
        // Eliminar el usuario
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado');
    }
}
