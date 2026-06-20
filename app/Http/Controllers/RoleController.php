<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

    public function index(Request $request)
    {
        $query = Role::query()
            ->with(['permissions:id,name'])
            ->where('guard_name', 'web')
            ->withCount('permissions')
            ->orderBy('name');

        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('permissions', function ($qp) use ($search) {
                        $qp->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = (int) $request->input('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50], true) ? $perPage : 15;

        $roles = $query->paginate($perPage)->withQueryString();

        return view('role.index', compact('roles', 'perPage'));
    }

    public function create()
    {
        $permisos = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name']);

        $permissionGroups = $this->groupPermissions($permisos);

        return view('role.create', compact('permissionGroups'));
    }

    public function store(StoreRoleRequest $request)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data) {
                $rol = Role::create([
                    'name' => strtolower($data['name']),
                    'guard_name' => 'web',
                ]);

                $rol->syncPermissions($data['permission'] ?? []);
            });

            return redirect()
                ->route('roles.index')
                ->with('success', 'Rol registrado correctamente');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al registrar el rol: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(Role $role)
    {
        $role->load('permissions:id,name');

        $permisos = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name']);

        $permissionGroups = $this->groupPermissions($permisos);
        $selectedPermissions = $role->permissions->pluck('id')->all();

        return view('role.edit', compact('role', 'permissionGroups', 'selectedPermissions'));
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        $data = $request->validated();

        try {
            DB::transaction(function () use ($data, $role) {
                $role->update([
                    'name' => strtolower($data['name']),
                ]);

                $role->syncPermissions($data['permission'] ?? []);
            });

            return redirect()
                ->route('roles.index')
                ->with('success', 'Rol actualizado correctamente');
        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error al editar el rol: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Role $role)
    {
        try {
            if (strtolower($role->name) === 'administrador') {
                return back()->withErrors([
                    'error' => 'El rol de Administrador principal no puede ser eliminado por razones de seguridad.',
                ]);
            }

            $role->delete();

            return redirect()
                ->route('roles.index')
                ->with('success', 'Rol eliminado correctamente');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al eliminar el rol: ' . $e->getMessage(),
            ]);
        }
    }

    private function groupPermissions(Collection $permisos): Collection
    {
        return $permisos->groupBy(function ($permiso) {
            return $this->permissionGroupLabel($permiso->name);
        })->sortKeys();
    }

    private function permissionGroupLabel(string $permissionName): string
    {
        $name = Str::of($permissionName)->lower();

        foreach (['gestionar_', 'ver_', 'registrar_', 'abrir_', 'cerrar_', 'anular_', 'movimientos_', 'editar_', 'actualizar_', 'eliminar_'] as $prefix) {
            if ($name->startsWith($prefix)) {
                $clean = (string) $name->after($prefix)->replace('_', ' ');
                return Str::headline($clean);
            }
        }

        return Str::headline(str_replace('_', ' ', $permissionName));
    }
}