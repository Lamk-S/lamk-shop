<?php

namespace Database\Seeders;

use App\Models\Documento;
use App\Models\Persona;
use App\Models\Proveedor;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $ruc = Documento::where('codigo', 'RUC')->firstOrFail();

        $proveedores = [
            [
                'numero_documento' => '20600000001',
                'tipo_persona' => 'juridica',
                'razon_social' => 'Distribuidora Deportiva Perú S.A.C.',
                'direccion' => 'Av. Industrial 456, Lima',
                'telefono' => '988777666',
                'email' => 'ventas@disdeportiva.test',
            ],
            [
                'numero_documento' => '20600000002',
                'tipo_persona' => 'juridica',
                'razon_social' => 'Importadora Sport Global S.A.C.',
                'direccion' => 'Jr. Comercio 789, Lima',
                'telefono' => '977666555',
                'email' => 'contacto@sportglobal.test',
            ],
        ];

        foreach ($proveedores as $item) {
            $persona = Persona::updateOrCreate(
                [
                    'documento_id' => $ruc->id,
                    'numero_documento' => $item['numero_documento'],
                ],
                [
                    'tipo_persona' => $item['tipo_persona'],
                    'nombres' => null,
                    'apellidos' => null,
                    'razon_social' => $item['razon_social'],
                    'direccion' => $item['direccion'],
                    'telefono' => $item['telefono'],
                    'email' => $item['email'],
                    'estado' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            Proveedor::updateOrCreate(
                ['persona_id' => $persona->id],
                [
                    'estado' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}