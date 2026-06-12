<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar_roles_permisos', only: ['index', 'create', 'store', 'edit', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $roles = Role::with('permissions')
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get();

        return view('role.index', compact('roles'));
    }

    public function create()
    {
        $permisos = Permission::where('guard_name', 'web')->orderBy('name')->get();

        return view('role.create', compact('permisos'));
    }

    public function store(StoreRoleRequest $request)
    {
        $data = $request->validated();

        try {
            $rol = Role::create([
                'name' => $data['name'],
                'guard_name' => 'web',
            ]);

            $rol->syncPermissions($data['permission']);

            return redirect()->route('roles.index')->with('success', 'Rol registrado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al registrar el rol: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function edit(Role $role)
    {
        $permisos = Permission::where('guard_name', 'web')->orderBy('name')->get();
        $role->load('permissions');

        return view('role.edit', compact('role', 'permisos'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $data = $request->validated();

        try {
            $role->update([
                'name' => $data['name'],
            ]);

            $role->syncPermissions($data['permission']);

            return redirect()->route('roles.index')->with('success', 'Rol actualizado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al editar el rol: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function destroy(Role $role)
    {
        try {
            if ($role->name === 'administrador') {
                return back()->withErrors([
                    'error' => 'El rol administrador no puede eliminarse.',
                ]);
            }

            $role->delete();

            return redirect()->route('roles.index')->with('success', 'Rol eliminado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al eliminar el rol: ' . $e->getMessage(),
            ]);
        }
    }
}