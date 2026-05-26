<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = Permission::pluck('name')->toArray();

        $roles = [
            'Administrador' => $permisos,

            'Vendedor' => [
                'ver-venta',
                'crear-venta',
                'mostrar-venta',
                'ver-cliente',
                'ver-producto',
                'ver-kardex',
                'ver-sesion-caja',
            ],

            'Cajero' => [
                'ver-caja',
                'ver-sesion-caja',
                'abrir-sesion-caja',
                'cerrar-sesion-caja',
                'ver-movimiento-caja',
                'crear-movimiento-caja',
                'ver-venta',
                'crear-venta',
                'mostrar-venta',
                'ver-pago-venta',
                'crear-pago-venta',
            ],

            'Almacenero' => [
                'ver-producto',
                'crear-producto',
                'editar-producto',
                'eliminar-producto',
                'ver-categoria',
                'crear-categoria',
                'editar-categoria',
                'eliminar-categoria',
                'ver-marca',
                'crear-marca',
                'editar-marca',
                'eliminar-marca',
                'ver-presentacion',
                'crear-presentacion',
                'editar-presentacion',
                'eliminar-presentacion',
                'ver-compra',
                'crear-compra',
                'mostrar-compra',
                'eliminar-compra',
                'ver-kardex',
            ],

            'Contabilidad' => [
                'ver-pago-venta',
                'crear-pago-venta',
                'ver-tesoreria',
                'ver-movimiento-tesoreria',
            ],
        ];

        foreach ($roles as $nombre => $listaPermisos) {
            $role = Role::firstOrCreate([
                'name' => $nombre,
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($listaPermisos);
        }
    }
}