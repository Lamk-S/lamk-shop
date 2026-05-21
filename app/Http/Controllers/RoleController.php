<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Override;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware() : array
    {
        return [
            new Middleware('permission:ver-role|crear-role|editar-role|eliminar-role', only: ['index']),
            new Middleware('permission:crear-role', only: ['create', 'store']),
            new Middleware('permission:editar-role', only: ['edit', 'update']),
            new Middleware('permission:eliminar-role', only: ['destroy']),
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::all();
        return view('role.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permisos = Permission::all();
        return view('role.create', compact('permisos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required|array',
            'permission.*' => 'exists:permissions,id'
        ]);

        try {
            DB::beginTransaction();

            // Crear rol
            $rol = Role::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);
            
            // Asignar permisos
            $permisos = Permission::whereIn('id', $request->permission)->get();
            $rol->syncPermissions($permisos);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }
        
        return redirect()->route('roles.index')->with('success', 'Rol registrado');
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
    public function edit(Role $role)
    {
        $permisos = Permission::all();
        return view('role.edit', compact('role', 'permisos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,'.$role->id,
            'permission' => 'required'
        ]);

        try {
            DB::beginTransaction();

            // Actualizar rol
            Role::where('id', $role->id)
            ->update([
                'name' => $request->name
            ]);

            // Actualizar permisos
            $permisos = Permission::whereIn('id', $request->permission)->get();
            $role->syncPermissions($permisos);
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
        }

        return redirect()->route('roles.index')->with('success', 'Rol editado');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Role::where('id', $id)->delete();
        return redirect()->route('roles.index')->with('success', 'Rol eliminado');
    }
}
