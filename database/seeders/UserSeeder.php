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
                'name' => 'Melvin López',
                'email' => 'admin@lamksports.test',
                'role' => 'administrador',
            ],
            [
                'name' => 'Tafarel Martínez',
                'email' => 'vendedor@lamksports.test',
                'role' => 'vendedor',
            ],
            [
                'name' => 'Beatriz Castillo',
                'email' => 'cajero@lamksports.test',
                'role' => 'cajero',
            ],
            [
                'name' => 'Erson Rosas',
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