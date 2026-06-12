<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComprobanteSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $items = [
            [
                'tipo_comprobante' => 'TICKET',
                'serie' => 'T001',
                'uso_comprobante' => 'VENTA',
            ],
            [
                'tipo_comprobante' => 'BOLETA',
                'serie' => 'B001',
                'uso_comprobante' => 'VENTA',
            ],
            [
                'tipo_comprobante' => 'FACTURA',
                'serie' => 'F001',
                'uso_comprobante' => 'VENTA',
            ],
            [
                'tipo_comprobante' => 'FACTURA',
                'serie' => 'C001',
                'uso_comprobante' => 'COMPRA',
            ],
        ];

        foreach ($items as $item) {
            DB::table('comprobantes')->updateOrInsert(
                [
                    'tipo_comprobante' => $item['tipo_comprobante'],
                    'serie' => $item['serie'],
                    'uso_comprobante' => $item['uso_comprobante'],
                ],
                [
                    'correlativo_actual' => 0,
                    'es_electronico' => false,
                    'ambiente' => 'SIMULADO',
                    'estado' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}