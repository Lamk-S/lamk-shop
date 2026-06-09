<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TesoreriaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $items = [
            [
                'codigo' => 'TES-EFECTIVO',
                'nombre' => 'Caja General',
                'tipo_cuenta' => 'EFECTIVO',
            ],
            [
                'codigo' => 'TES-BANCO',
                'nombre' => 'Banco Principal',
                'tipo_cuenta' => 'BANCO',
            ],
        ];

        foreach ($items as $item) {
            DB::table('tesorerias')->updateOrInsert(
                ['codigo' => $item['codigo']],
                [
                    'nombre' => $item['nombre'],
                    'tipo_cuenta' => $item['tipo_cuenta'],
                    'saldo_actual' => 0,
                    'estado' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}