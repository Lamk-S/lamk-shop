<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            'ver-categoria',
            'crear-categoria',
            'editar-categoria',
            'eliminar-categoria',

            'ver-cliente',
            'crear-cliente',
            'editar-cliente',
            'eliminar-cliente',

            'ver-compra',
            'crear-compra',
            'mostrar-compra',
            'eliminar-compra',

            'ver-marca',
            'crear-marca',
            'editar-marca',
            'eliminar-marca',

            'ver-presentacion',
            'crear-presentacion',
            'editar-presentacion',
            'eliminar-presentacion',

            'ver-producto',
            'crear-producto',
            'editar-producto',
            'eliminar-producto',

            'ver-proveedor',
            'crear-proveedor',
            'editar-proveedor',
            'eliminar-proveedor',

            'ver-venta',
            'crear-venta',
            'mostrar-venta',
            'eliminar-venta',

            'ver-role',
            'crear-role',
            'editar-role',
            'eliminar-role',

            'ver-user',
            'crear-user',
            'editar-user',
            'eliminar-user',

            'ver-caja',
            'crear-caja',
            'editar-caja',
            'eliminar-caja',

            'ver-sesion-caja',
            'abrir-sesion-caja',
            'cerrar-sesion-caja',

            'ver-movimiento-caja',
            'crear-movimiento-caja',

            'ver-kardex',

            'ver-pago-venta',
            'crear-pago-venta',

            'ver-tesoreria',
            'ver-movimiento-tesoreria',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web',
            ]);
        }
    }
}