<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DocumentoSeeder::class,
            EmpresaConfiguracionSeeder::class,
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            CatalogosSeeder::class,
            TallaSeeder::class,
            ComprobantesSeeder::class,
            CajaSeeder::class,
            TesoreriaSeeder::class,
            ProductoSeeder::class,
            ProductoVariantesSeeder::class,
        ]);
    }
}