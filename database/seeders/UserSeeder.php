<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrador Lamk',
                'email' => 'admin@lamksports.test',
                'role' => 'administrador',
            ],
            [
                'name' => 'Vendedor Principal',
                'email' => 'vendedor@lamksports.test',
                'role' => 'vendedor',
            ],
            [
                'name' => 'Cajero Principal',
                'email' => 'cajero@lamksports.test',
                'role' => 'cajero',
            ],
            [
                'name' => 'Encargado de Almacén',
                'email' => 'almacen@lamksports.test',
                'role' => 'encargado_almacen',
            ],
        ];

        foreach ($users as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password'),
                    'estado' => 1,
                ]
            );

            $user->syncRoles([$data['role']]);
        }
    }
}