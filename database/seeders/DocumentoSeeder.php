<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $items = [
            ['codigo' => 'DNI', 'tipo_documento' => 'Documento Nacional de Identidad'],
            ['codigo' => 'RUC', 'tipo_documento' => 'Registro Único de Contribuyentes'],
            ['codigo' => 'CE', 'tipo_documento' => 'Carné de Extranjería'],
            ['codigo' => 'PAS', 'tipo_documento' => 'Pasaporte'],
            ['codigo' => 'OTRO', 'tipo_documento' => 'Otro documento'],
        ];

        foreach ($items as $item) {
            DB::table('documentos')->updateOrInsert(
                ['codigo' => $item['codigo']],
                [
                    'tipo_documento' => $item['tipo_documento'],
                    'estado' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}