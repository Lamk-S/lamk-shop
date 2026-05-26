<?php

namespace Database\Seeders;

use App\Models\Comprobante;
use Illuminate\Database\Seeder;

class ComprobanteSeeder extends Seeder
{
    public function run(): void
    {
        $comprobantes = [
            ['tipo_comprobante' => 'Boleta', 'serie' => 'B001', 'correlativo_actual' => 0],
            ['tipo_comprobante' => 'Factura', 'serie' => 'F001', 'correlativo_actual' => 0],
        ];

        foreach ($comprobantes as $item) {
            Comprobante::firstOrCreate(
                [
                    'tipo_comprobante' => $item['tipo_comprobante'],
                    'serie' => $item['serie'],
                ],
                [
                    'correlativo_actual' => $item['correlativo_actual'],
                    'estado' => 1,
                ]
            );
        }
    }
}