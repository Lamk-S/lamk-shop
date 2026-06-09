<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CajaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('cajas')->updateOrInsert(
            ['codigo' => 'CAJ-01'],
            [
                'nombre' => 'Caja Principal',
                'fondo_fijo' => 100.00,
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('cajas')->updateOrInsert(
            ['codigo' => 'CAJ-02'],
            [
                'nombre' => 'Caja Secundaria',
                'fondo_fijo' => 50.00,
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}