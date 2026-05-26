<?php

namespace Database\Seeders;

use App\Models\Documento;
use App\Models\Persona;
use Illuminate\Database\Seeder;

class ProveedorSeeder extends Seeder
{
    public function run(): void
    {
        $ruc = Documento::where('tipo_documento', 'RUC')->firstOrFail()->id;

        $proveedores = [
            [
                'razon_social' => 'Distribuidora Lima S.A.C.',
                'direccion' => 'Av. Industrial 123, Lima',
                'telefono' => '987100001',
                'email' => 'ventas@distribuidoralima.pe',
                'tipo_persona' => 'juridica',
                'documento_id' => $ruc,
                'numero_documento' => '20111111111',
            ],
            [
                'razon_social' => 'Inversiones Global Perú S.A.C.',
                'direccion' => 'Av. Arequipa 456, Lima',
                'telefono' => '987100002',
                'email' => 'contacto@globalperu.pe',
                'tipo_persona' => 'juridica',
                'documento_id' => $ruc,
                'numero_documento' => '20122222222',
            ],
            [
                'razon_social' => 'Comercial Andina E.I.R.L.',
                'direccion' => 'Jr. Amazonas 789, Huancayo',
                'telefono' => '987100003',
                'email' => 'ventas@comercialandina.pe',
                'tipo_persona' => 'juridica',
                'documento_id' => $ruc,
                'numero_documento' => '20133333333',
            ],
            [
                'razon_social' => 'Distribuciones Norte S.A.C.',
                'direccion' => 'Av. España 101, Trujillo',
                'telefono' => '987100004',
                'email' => 'contacto@distribucionesnorte.pe',
                'tipo_persona' => 'juridica',
                'documento_id' => $ruc,
                'numero_documento' => '20144444444',
            ],
            [
                'razon_social' => 'Mayorista Sur Perú S.A.C.',
                'direccion' => 'Av. Los Incas 202, Arequipa',
                'telefono' => '987100005',
                'email' => 'ventas@mayoristasur.pe',
                'tipo_persona' => 'juridica',
                'documento_id' => $ruc,
                'numero_documento' => '20155555555',
            ],
        ];

        foreach ($proveedores as $item) {
            $persona = Persona::firstOrCreate(
                ['numero_documento' => $item['numero_documento']],
                [
                    'razon_social' => $item['razon_social'],
                    'direccion' => $item['direccion'],
                    'telefono' => $item['telefono'],
                    'email' => $item['email'],
                    'tipo_persona' => $item['tipo_persona'],
                    'documento_id' => $item['documento_id'],
                    'estado' => 1,
                ]
            );

            if (!$persona->proveedor) {
                $persona->proveedor()->create();
            }
        }
    }
}