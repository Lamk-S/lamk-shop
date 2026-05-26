<?php

namespace Database\Seeders;

use App\Models\Caja;
use Illuminate\Database\Seeder;

class CajaSeeder extends Seeder
{
    public function run(): void
    {
        $cajas = [
            ['nombre' => 'Caja Principal', 'fondo_fijo' => 100.00],
            ['nombre' => 'Caja Secundaria', 'fondo_fijo' => 100.00],
        ];

        foreach ($cajas as $item) {
            Caja::firstOrCreate(
                ['nombre' => $item['nombre']],
                [
                    'fondo_fijo' => $item['fondo_fijo'],
                    'estado' => 1,
                ]
            );
        }
    }
}