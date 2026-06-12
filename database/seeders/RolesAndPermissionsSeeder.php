<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'ver_dashboard',
            'gestionar_usuarios',
            'gestionar_roles_permisos',
            'gestionar_configuracion',
            'gestionar_clientes',
            'gestionar_proveedores',
            'gestionar_productos',
            'gestionar_categorias',
            'gestionar_marcas',
            'gestionar_tallas',
            'gestionar_cajas',
            'abrir_caja',
            'cerrar_caja',
            'movimientos_caja',
            'gestionar_tesoreria',
            'registrar_compras',
            'anular_compras',
            'registrar_ventas',
            'anular_ventas',
            'ver_kardex',
            'ver_auditoria',
            'gestionar_comprobantes',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $roles = [
            'administrador',
            'vendedor',
            'cajero',
            'encargado_almacen',
        ];

        foreach ($roles as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }

        Role::findByName('administrador', 'web')->givePermissionTo(Permission::all());

        Role::findByName('vendedor', 'web')->givePermissionTo([
            'ver_dashboard',
            'gestionar_clientes',
            'gestionar_productos',
            'registrar_ventas',
            'gestionar_comprobantes',
        ]);

        Role::findByName('cajero', 'web')->givePermissionTo([
            'ver_dashboard',
            'gestionar_clientes',
            'registrar_ventas',
            'anular_ventas',
            'abrir_caja',
            'cerrar_caja',
            'movimientos_caja',
            'gestionar_comprobantes',
        ]);

        Role::findByName('encargado_almacen', 'web')->givePermissionTo([
            'ver_dashboard',
            'gestionar_proveedores',
            'gestionar_productos',
            'gestionar_categorias',
            'gestionar_marcas',
            'gestionar_tallas',
            'registrar_compras',
            'anular_compras',
            'ver_kardex',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}