<?php

namespace Database\Seeders;

use App\Models\Comprobante;
use Illuminate\Database\Seeder;

class ComprobanteSeeder extends Seeder
{
    public function run(): void
    {
        $comprobantes = [
            ['tipo_comprobante' => 'Boleta',  'uso_comprobante' => 'VENTA',  'serie' => 'BOLVENT', 'correlativo_actual' => 0],
            ['tipo_comprobante' => 'Boleta',  'uso_comprobante' => 'COMPRA', 'serie' => 'BOLCOMP', 'correlativo_actual' => 0],
            ['tipo_comprobante' => 'Factura', 'uso_comprobante' => 'VENTA',  'serie' => 'FACVENT', 'correlativo_actual' => 0],
            ['tipo_comprobante' => 'Factura', 'uso_comprobante' => 'COMPRA', 'serie' => 'FACCOMP', 'correlativo_actual' => 0],
        ];

        foreach ($comprobantes as $item) {
            Comprobante::firstOrCreate(
                ['serie' => $item['serie']],
                [
                    'tipo_comprobante' => $item['tipo_comprobante'],
                    'uso_comprobante' => $item['uso_comprobante'],
                    'correlativo_actual' => $item['correlativo_actual'],
                    'estado' => 1,
                ]
            );
        }
    }
}