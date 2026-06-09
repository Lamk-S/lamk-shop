<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TallaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $items = [];

        foreach (range(35, 45) as $size) {
            $items[] = [
                'codigo' => (string) $size,
                'nombre' => "Talla {$size}",
                'tipo_talla' => 'CALZADO',
                'orden' => $size,
            ];
        }

        foreach ([
            ['codigo' => 'XS',  'nombre' => 'XS',      'tipo_talla' => 'ROPA', 'orden' => 1],
            ['codigo' => 'S',   'nombre' => 'S',       'tipo_talla' => 'ROPA', 'orden' => 2],
            ['codigo' => 'M',   'nombre' => 'M',       'tipo_talla' => 'ROPA', 'orden' => 3],
            ['codigo' => 'L',   'nombre' => 'L',       'tipo_talla' => 'ROPA', 'orden' => 4],
            ['codigo' => 'XL',  'nombre' => 'XL',      'tipo_talla' => 'ROPA', 'orden' => 5],
            ['codigo' => 'XXL', 'nombre' => 'XXL',     'tipo_talla' => 'ROPA', 'orden' => 6],
            ['codigo' => 'UNICA', 'nombre' => 'Única', 'tipo_talla' => 'UNICA', 'orden' => 99],
        ] as $item) {
            $items[] = $item;
        }

        foreach ($items as $item) {
            DB::table('tallas')->updateOrInsert(
                ['codigo' => $item['codigo']],
                [
                    'nombre' => $item['nombre'],
                    'tipo_talla' => $item['tipo_talla'],
                    'orden' => $item['orden'],
                    'estado' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}