<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario
        $user = User::firstOrCreate(
            ['email' => 'kunlancelot@gmail.com'],
            [
                'name' => 'Melvin López',
                'password' => bcrypt('password')
            ]
        );

        // Crear rol administrador
        $rol = Role::firstOrCreate([
            'name' => 'Administrador',
            'guard_name' => 'web'
        ]);

        // Obtener permisos
        $permisos = Permission::pluck('id', 'id')->all();

        // Asignar permisos al rol
        $rol->syncPermissions($permisos);

        // Asignar rol al usuario
        $user->assignRole($rol);
    }
}
