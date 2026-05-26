<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DocumentoSeeder::class,
            CategoriaSeeder::class,
            MarcaSeeder::class,
            PresentacionSeeder::class,
            ComprobanteSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            CajaSeeder::class,
            TesoreriaSeeder::class,
            ClienteSeeder::class,
            ProveedorSeeder::class,
            ProductoSeeder::class,
        ]);
    }
}