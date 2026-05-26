<?php

namespace Database\Seeders;

use App\Models\Documento;
use App\Models\Persona;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $dni = Documento::where('tipo_documento', 'DNI')->firstOrFail()->id;
        $pasaporte = Documento::where('tipo_documento', 'Pasaporte')->firstOrFail()->id;
        $ruc = Documento::where('tipo_documento', 'RUC')->firstOrFail()->id;
        $ce = Documento::where('tipo_documento', 'Carnet Extranjería')->firstOrFail()->id;

        $clientes = [
            [
                'razon_social' => 'Juan Carlos Pérez',
                'direccion' => 'Av. Los Olivos 123, Lima',
                'telefono' => '987654321',
                'email' => 'juan.perez@email.com',
                'tipo_persona' => 'natural',
                'documento_id' => $dni,
                'numero_documento' => '72345678',
            ],
            [
                'razon_social' => 'Michael Smith',
                'direccion' => 'Calle Las Palmeras 456, Arequipa',
                'telefono' => '976543210',
                'email' => 'michael.smith@email.com',
                'tipo_persona' => 'natural',
                'documento_id' => $pasaporte,
                'numero_documento' => 'P1234567',
            ],
            [
                'razon_social' => 'Inversiones ABC S.A.C.',
                'direccion' => 'Av. Argentina 1010, Callao',
                'telefono' => '944332211',
                'email' => 'contacto@abc.com.pe',
                'tipo_persona' => 'juridica',
                'documento_id' => $ruc,
                'numero_documento' => '20123456789',
            ],
            [
                'razon_social' => 'Carlos Mendoza',
                'direccion' => 'Urb. San Borja Mz A Lt 3',
                'telefono' => '999111222',
                'email' => 'carlos.mendoza@email.com',
                'tipo_persona' => 'natural',
                'documento_id' => $ce,
                'numero_documento' => '001122334',
            ],
        ];

        foreach ($clientes as $item) {
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

            if (!$persona->cliente) {
                $persona->cliente()->create();
            }
        }
    }
}