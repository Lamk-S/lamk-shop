<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Melvin López',
                'email' => 'kunlancelot@gmail.com',
                'password' => 'password',
                'role' => 'Administrador',
            ],
            [
                'name' => 'Usuario Vendedor',
                'email' => 'vendedor@demo.com',
                'password' => 'password',
                'role' => 'Vendedor',
            ],
            [
                'name' => 'Usuario Cajero',
                'email' => 'cajero@demo.com',
                'password' => 'password',
                'role' => 'Cajero',
            ],
        ];

        foreach ($usuarios as $item) {
            $user = User::firstOrCreate(
                ['email' => $item['email']],
                [
                    'name' => $item['name'],
                    'password' => Hash::make($item['password']),
                    'estado' => 1,
                ]
            );

            $role = Role::where('name', $item['role'])->first();
            if ($role && !$user->hasRole($role->name)) {
                $user->syncRoles([$role->name]);
            }
        }
    }
}