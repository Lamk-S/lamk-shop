<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmpresaConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('empresa_configuracion')->updateOrInsert(
            ['ruc' => '20123456789'],
            [
                'razon_social' => 'Lamk Sports S.A.C.',
                'nombre_comercial' => 'Lamk Sports',
                'direccion_fiscal' => 'Av. Principal 123, Lima, Perú',
                'telefono' => '999888777',
                'email' => 'contacto@lamksports.test',
                'logo_path' => null,
                'mensaje_ticket' => 'Gracias por su compra. Vuelva pronto.',
                'moneda' => 'PEN',
                'igv_porcentaje' => 18.00,
                'modo_emision' => 'SIMULADA',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}