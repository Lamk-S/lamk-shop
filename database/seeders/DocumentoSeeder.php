<?php

namespace Database\Seeders;

use App\Models\Documento;
use Illuminate\Database\Seeder;

class DocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $documentos = [
            'DNI',
            'Pasaporte',
            'RUC',
            'Carnet Extranjería',
        ];

        foreach ($documentos as $tipo) {
            Documento::firstOrCreate([
                'tipo_documento' => $tipo,
            ], [
                'estado' => 1,
            ]);
        }
    }
}