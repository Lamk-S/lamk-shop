<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver-role|crear-role|editar-role|eliminar-role', only: ['index']),
            new Middleware('permission:crear-role', only: ['create', 'store']),
            new Middleware('permission:editar-role', only: ['edit', 'update']),
            new Middleware('permission:eliminar-role', only: ['destroy']),
        ];
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('role.index', compact('roles'));
    }

    public function create()
    {
        $permisos = Permission::all();
        return view('role.create', compact('permisos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required|array',
            'permission.*' => 'exists:permissions,id',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $rol = Role::create([
                    'name' => $request->name,
                    'guard_name' => 'web',
                ]);

                $rol->syncPermissions($request->permission);
            });

            return redirect()->route('roles.index')->with('success', 'Rol registrado');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar el rol: ' . $e->getMessage()]);
        }
    }

    public function edit(Role $role)
    {
        $permisos = Permission::all();
        $role->load('permissions');

        return view('role.edit', compact('role', 'permisos'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $role->id,
            'permission' => 'required|array',
            'permission.*' => 'exists:permissions,id',
        ]);

        try {
            DB::transaction(function () use ($request, $role) {
                $role->update([
                    'name' => $request->name,
                ]);

                $role->syncPermissions($request->permission);
            });

            return redirect()->route('roles.index')->with('success', 'Rol editado');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al editar el rol: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            Role::findOrFail($id)->delete();
            return redirect()->route('roles.index')->with('success', 'Rol eliminado');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar el rol: ' . $e->getMessage()]);
        }
    }
}