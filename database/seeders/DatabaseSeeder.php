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
            ClienteGenericoSeeder::class,
            ProveedorSeeder::class,
            CatalogosSeeder::class,
            TallaSeeder::class,
            CajaSeeder::class,
            TesoreriaSeeder::class,
            ComprobanteSeeder::class,
            ProductoSeeder::class,
            ProductoVariantesSeeder::class,
        ]);
    }
}